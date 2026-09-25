<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_dashboard_shows_tasks_and_status_counts(): void
    {
        Task::factory()->create(['title' => 'Water the plants']);
        Task::factory()->completed()->create(['title' => 'Finish my book']);

        $this->get(route('tasks.index'))
            ->assertSee('Water the plants')
            ->assertSee('Finish my book')
            ->assertSee('Everything')
            ->assertSee('href="/tasks/create"', false)
            ->assertSee('action="/tasks"', false)
            ->assertDontSee('http://', false);
    }

    public function test_dashboard_filters_tasks_by_status(): void
    {
        Task::factory()->create(['title' => 'Fold the laundry']);
        Task::factory()->completed()->create(['title' => 'Buy a notebook']);

        $this->get(route('tasks.index', ['status' => 'pending']))
            ->assertSee('Fold the laundry')
            ->assertDontSee('Buy a notebook');

        $this->get(route('tasks.index', ['status' => 'completed']))
            ->assertSee('Buy a notebook')
            ->assertDontSee('Fold the laundry');
    }

    public function test_dashboard_escapes_task_titles_and_notes(): void
    {
        $dangerousText = '<script>alert(1)</script>';
        Task::factory()->create(['title' => $dangerousText, 'notes' => $dangerousText]);

        $this->get(route('tasks.index'))
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee($dangerousText, false);
    }

    public function test_a_task_can_be_created_and_validated(): void
    {
        $this->post(route('tasks.store'), [
            'title' => 'Pick up fresh flowers',
            'notes' => 'Choose pink tulips',
            'due_date' => '2030-06-15',
            'status' => 'completed',
        ])->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Pick up fresh flowers',
            'notes' => 'Choose pink tulips',
            'status' => 'pending',
        ]);

        $this->from(route('tasks.create'))
            ->post(route('tasks.store'), ['title' => ''])
            ->assertSessionHasErrors(['title' => 'The title field is required.']);

        $this->assertDatabaseCount('tasks', 1);
    }

    public function test_task_creation_rejects_invalid_due_dates_and_oversized_notes(): void
    {
        $this->from(route('tasks.create'))
            ->post(route('tasks.store'), [
                'title' => 'Plan a cozy evening',
                'notes' => str_repeat('a', 1201),
                'due_date' => 'not-a-date',
            ])
            ->assertSessionHasErrors([
                'notes' => 'The notes field must not be greater than 1200 characters.',
                'due_date' => 'The due date field must be a valid date.',
            ]);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_creation_rejects_non_string_titles_and_oversized_titles(): void
    {
        $this->from(route('tasks.create'))
            ->post(route('tasks.store'), ['title' => ['not', 'text']])
            ->assertSessionHasErrors(['title' => 'The title field must be a string.']);

        $this->from(route('tasks.create'))
            ->post(route('tasks.store'), [
                'title' => str_repeat('a', 121),
                'notes' => ['not', 'text'],
            ])
            ->assertSessionHasErrors([
                'title' => 'The title field must not be greater than 120 characters.',
                'notes' => 'The notes field must be a string.',
            ]);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_a_task_can_be_edited_and_its_status_changed(): void
    {
        $task = Task::factory()->create(['title' => 'Read a chapter']);

        $this->get(route('tasks.edit', $task))
            ->assertSee('Read a chapter')
            ->assertSee('Status');

        $this->put(route('tasks.update', $task), [
            'title' => 'Read two chapters',
            'notes' => 'Before bed',
            'due_date' => null,
            'status' => 'completed',
        ])->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Read two chapters',
            'notes' => 'Before bed',
            'status' => 'completed',
        ]);
    }

    public function test_task_updates_reject_missing_titles_and_invalid_statuses(): void
    {
        $task = Task::factory()->create(['title' => 'Stretch after lunch']);

        $this->put(route('tasks.update', $task), [
            'title' => '',
            'notes' => null,
            'due_date' => null,
            'status' => 'finished-ish',
        ])->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'status' => 'The selected status is invalid.',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Stretch after lunch',
            'status' => 'pending',
        ]);
    }

    public function test_a_task_status_can_be_toggled_and_a_task_deleted(): void
    {
        $task = Task::factory()->create(['title' => 'Tidy my desk']);

        $this->patch(route('tasks.status.update', $task))->assertRedirect();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);

        $this->patch(route('tasks.status.update', $task))->assertRedirect();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'pending']);

        $this->delete(route('tasks.destroy', $task))->assertRedirect('/tasks');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
