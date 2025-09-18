<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('ssh_details'); // Drop the old column

            $table->string('ssh_username')->nullable()->after('connection_type');
            $table->text('ssh_password')->nullable()->after('ssh_username'); // Encrypted
            $table->text('ssh_private_key')->nullable()->after('ssh_password'); // Encrypted
            $table->integer('ssh_port')->default(22)->after('ssh_private_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('ssh_username');
            $table->dropColumn('ssh_password');
            $table->dropColumn('ssh_private_key');
            $table->dropColumn('ssh_port');

            $table->text('ssh_details')->nullable(); // Re-add the old column if rolling back
        });
    }
};
