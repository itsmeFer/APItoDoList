<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secure_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // e.g., "ATM BCA", "Email Password"
            $table->text('content'); // Encrypted content
            $table->enum('type', ['password', 'pin', 'card', 'note', 'other'])->default('note');
            $table->string('icon')->nullable(); // Icon name
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('is_favorite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secure_notes');
    }
};