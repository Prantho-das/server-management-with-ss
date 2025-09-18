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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('process_name')->nullable();
            $table->integer('port')->nullable();
            $table->string('version')->nullable();
            $table->string('status')->default('unknown');
            $table->float('cpu_usage')->nullable();
            $table->float('memory_usage')->nullable();
            $table->timestamps();

            $table->unique(['server_id', 'name']); // A server should not have two services with the same name
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
