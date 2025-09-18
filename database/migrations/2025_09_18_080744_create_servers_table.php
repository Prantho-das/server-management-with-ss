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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address')->unique();
            $table->string('hostname')->unique();
            $table->text('ssh_details')->nullable(); // Will be encrypted later
            $table->string('os')->nullable();
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('disk')->nullable();
            $table->string('status')->default('unknown');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
