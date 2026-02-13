<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Todo;
use Illuminate\Support\Facades\Hash;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@taskflow.com'],
            ['name' => 'Demo User', 'password' => Hash::make('password')]
        );

        Todo::where('user_id', $user->id)->delete();

        $todos = [
            ['title' => 'Complete project proposal', 'category' => 'Work', 'priority' => 'High', 'due_date' => now()->addDays(2)],
            ['title' => 'Buy groceries', 'category' => 'Shopping', 'priority' => 'Medium', 'due_date' => now()->addDay()],
            ['title' => 'Morning workout', 'category' => 'Health', 'priority' => 'High', 'is_completed' => true],
        ];

        foreach ($todos as $todo) {
            Todo::create(array_merge(['user_id' => $user->id], $todo));
        }
    }
}