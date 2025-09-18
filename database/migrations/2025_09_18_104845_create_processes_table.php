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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->integer('pid');
            $table->string('user');
            $table->text('command');
            $table->float('cpu_percent');
            $table->float('memory_percent');
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamps();

            $table->unique(['server_id', 'pid']); // A server should have unique PIDs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
