<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('secure_pin')->nullable()->after('password');
            $table->timestamp('secure_pin_set_at')->nullable()->after('secure_pin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['secure_pin', 'secure_pin_set_at']);
        });
    }
};