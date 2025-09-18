<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Alert; // Import the Alert model
use App\Models\Setting; // Import the Setting model
use Illuminate\Notifications\Messages\SlackMessage; // Import SlackMessage
use NotificationChannels\Discord\DiscordMessage; // Import DiscordMessage

class AlertNotification extends Notification
{
    use Queueable;

    protected $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        if (Setting::where('key', 'slack_webhook_url')->exists()) {
            $channels[] = 'slack';
        }
        if (Setting::where('key', 'discord_webhook_url')->exists()) {
            $channels[] = 'discord';
        }
        // Add SMS channel if configured
        // if (Setting::where('key', 'twilio_sid')->exists() && Setting::where('key', 'twilio_token')->exists()) {
        //     $channels[] = 'twilio'; // Assuming you have a Twilio channel setup
        // }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Alert: {$this->alert->severity} - {$this->alert->type}";
        $message = "An alert has been triggered:\n\n";
        $message .= "Server: " . ($this->alert->server ? $this->alert->server->name : 'N/A') . "\n";
        $message .= "Service: " . ($this->alert->service ? $this->alert->service->name : 'N/A') . "\n";
        $message .= "Type: {$this->alert->type}\n";
        $message .= "Severity: {$this->alert->severity}\n";
        $message .= "Message: {$this->alert->message}\n";
        $message .= "Triggered At: {$this->alert->triggered_at->toDateTimeString()}\n";

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action('View Alert', url('/admin/alerts/' . $this->alert->id))
            ->line('Please investigate this alert.');
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        $slackWebhookUrl = Setting::where('key', 'slack_webhook_url')->value('value');

        return (new SlackMessage)
            ->to($slackWebhookUrl) // Use webhook URL from settings
            ->from('Monitoring Bot', ':robot_face:')
            ->content("Alert: {$this->alert->severity} - {$this->alert->type}")
            ->attachment(function ($attachment) {
                $attachment->title('Alert Details', url('/admin/alerts/' . $this->alert->id))
                    ->fields([
                        'Server' => $this->alert->server ? $this->alert->server->name : 'N/A',
                        'Service' => $this->alert->service ? $this->alert->service->name : 'N/A',
                        'Type' => $this->alert->type,
                        'Severity' => $this->alert->severity,
                        'Message' => $this->alert->message,
                        'Triggered At' => $this->alert->triggered_at->toDateTimeString(),
                    ])
                    ->color($this->getSlackColor($this->alert->severity));
            });
    }

    /**
     * Get the Discord representation of the notification.
     */
    public function toDiscord(object $notifiable): DiscordMessage
    {
        $discordWebhookUrl = Setting::where('key', 'discord_webhook_url')->value('value');

        return DiscordMessage::create()
            ->to($discordWebhookUrl) // Use webhook URL from settings
            ->from('Monitoring Bot', 'https://example.com/avatar.png') // Replace with actual avatar
            ->title("Alert: {$this->alert->severity} - {$this->alert->type}")
            ->description($this->alert->message)
            ->url(url('/admin/alerts/' . $this->alert->id))
            ->timestamp($this->alert->triggered_at)
            ->color($this->getDiscordColor($this->alert->severity))
            ->fields([
                'Server' => $this->alert->server ? $this->alert->server->name : 'N/A',
                'Service' => $this->alert->service ? $this->alert->service->name : 'N/A',
                'Type' => $this->alert->type,
                'Severity' => $this->alert->severity,
            ]);
    }

    /**
     * Get the color for Slack message based on severity.
     */
    protected function getSlackColor(string $severity): string
    {
        return match ($severity) {
            'info' => '#4CAF50', // Green
            'warning' => '#FFC107', // Amber
            'critical' => '#F44336', // Red
            default => '#CCCCCC', // Grey
        };
    }

    /**
     * Get the color for Discord message based on severity.
     */
    protected function getDiscordColor(string $severity): int
    {
        return match ($severity) {
            'info' => 0x4CAF50, // Green
            'warning' => 0xFFC107, // Amber
            'critical' => 0xF44336, // Red
            default => 0xCCCCCC, // Grey
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'type' => $this->alert->type,
            'severity' => $this->alert->severity,
            'message' => $this->alert->message,
            'server_name' => $this->alert->server ? $this->alert->server->name : null,
            'service_name' => $this->alert->service ? $this->alert->service->name : null,
        ];
    }
}
