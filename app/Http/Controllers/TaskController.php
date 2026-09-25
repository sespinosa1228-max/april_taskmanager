<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('status', 'all');

        if (! in_array($filter, ['all', 'pending', 'completed'], true)) {
            $filter = 'all';
        }

        $tasks = Task::query()
            ->when($filter !== 'all', fn ($query) => $query->where('status', $filter))
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest('created_at')
            ->get();

        $taskCounts = Task::query()
            ->toBase()
            ->selectRaw('COUNT(*) AS all_count')
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) AS pending', ['pending'])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) AS completed', ['completed'])
            ->selectRaw('COUNT(CASE WHEN status = ? AND due_date = ? THEN 1 END) AS today', ['pending', today()->toDateString()])
            ->first();

        return view('tasks.index', [
            'tasks' => $tasks,
            'filter' => $filter,
            'taskCounts' => $taskCounts,
            'pendingTaskCount' => (int) $taskCounts->pending,
        ]);
    }

    public function create(): View
    {
        return view('tasks.create', ['pendingTaskCount' => $this->pendingTaskCount()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1200'],
            'due_date' => ['nullable', 'date'],
        ]);

        Task::create([...$validated, 'status' => 'pending']);

        return $this->redirectToTaskList($request, 'Your new task is on the list.');
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', [
            'task' => $task,
            'pendingTaskCount' => $this->pendingTaskCount(),
        ]);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1200'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,completed'],
        ]);

        $task->update($validated);

        return $this->redirectToTaskList($request, 'Task updated.');
    }

    public function toggleStatus(Request $request, Task $task): RedirectResponse
    {
        $task->update([
            'status' => $task->status === 'pending' ? 'completed' : 'pending',
        ]);

        return back()->with('success', $task->status === 'completed' ? 'Task completed. Lovely work!' : 'Task moved back to pending.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $task->delete();

        return $this->redirectToTaskList($request, 'Task deleted.');
    }

    private function pendingTaskCount(): int
    {
        return Task::query()->where('status', 'pending')->count();
    }

    private function redirectToTaskList(Request $request, string $message): RedirectResponse
    {
        return (new RedirectResponse(route('tasks.index', [], false)))
            ->setRequest($request)
            ->setSession($request->session())
            ->with('success', $message);
    }
}
