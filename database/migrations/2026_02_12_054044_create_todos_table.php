<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['Personal', 'Work', 'Shopping', 'Health', 'Learning'])->default('Personal');
            $table->enum('priority', ['High', 'Medium', 'Low'])->default('Medium');
            $table->boolean('is_completed')->default(false);
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('category');
            $table->index('priority');
            $table->index('is_completed');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};