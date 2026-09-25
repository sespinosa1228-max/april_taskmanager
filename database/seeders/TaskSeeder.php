<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::factory()->create([
            'title' => 'Plan the week',
            'notes' => 'Pick three things to focus on.',
            'due_date' => today()->addDay(),
        ]);

        Task::factory()->create([
            'title' => 'Water the plants',
            'notes' => 'The little ones by the window, too.',
            'due_date' => today(),
        ]);

        Task::factory()->create([
            'title' => 'Tidy the desk',
            'status' => 'completed',
        ]);
    }
}
