@extends('layouts.app')

@section('title', 'My tasks | Petal & Plans')

@section('content')
    <section class="welcome-row">
        <div>
            <p class="eyebrow">YOUR PERSONAL TASK NOOK</p>
            <h1>A fresh little start<span class="title-period">.</span></h1>
            <p class="welcome-copy">One thing at a time. You have got this.</p>
        </div>
        <a class="button button-primary welcome-add" href="{{ route('tasks.create', [], false) }}"><span aria-hidden="true">＋</span> Add a task</a>
    </section>

    <section class="stat-grid" aria-label="Task overview">
        <div class="stat-item"><span class="stat-icon stat-icon-pink" aria-hidden="true">○</span><span class="stat-label">On your list</span><strong>{{ $taskCounts->all_count }}</strong></div>
        <div class="stat-item"><span class="stat-icon stat-icon-berry" aria-hidden="true">◷</span><span class="stat-label">Still to do</span><strong>{{ $taskCounts->pending }}</strong></div>
        <div class="stat-item"><span class="stat-icon stat-icon-gold" aria-hidden="true">✦</span><span class="stat-label">All done</span><strong>{{ $taskCounts->completed }}</strong></div>
        <div class="stat-item"><span class="stat-icon stat-icon-peach" aria-hidden="true">♡</span><span class="stat-label">Due today</span><strong>{{ $taskCounts->today }}</strong></div>
    </section>

    <div class="dashboard-grid">
        <section class="task-panel" aria-labelledby="list-heading">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">THE LITTLE THINGS</p>
                    <h2 id="list-heading">My task list</h2>
                </div>
                <span class="list-date">{{ now()->format('M j') }}</span>
            </div>

            <nav class="filter-tabs" aria-label="Filter tasks">
                @foreach (['all' => 'Everything', 'pending' => 'To do', 'completed' => 'Done'] as $key => $label)
                    <a class="filter-tab {{ $filter === $key ? 'is-selected' : '' }}" href="{{ route('tasks.index', ['status' => $key === 'all' ? null : $key], false) }}" @if ($filter === $key) aria-current="page" @endif>
                        {{ $label }} <span>{{ $key === 'all' ? $taskCounts->all_count : $taskCounts->{$key} }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="task-list">
                @forelse ($tasks as $task)
                    <article class="task-row {{ $task->status === 'completed' ? 'task-completed' : '' }}">
                        <form method="POST" action="{{ route('tasks.status.update', $task, false) }}" class="status-form">
                            @csrf
                            @method('PATCH')
                            <button class="status-toggle {{ $task->status === 'completed' ? 'is-checked' : '' }}" type="submit" aria-label="{{ $task->status === 'completed' ? 'Mark '.$task->title.' as pending' : 'Mark '.$task->title.' as completed' }}">
                                @if ($task->status === 'completed') <span aria-hidden="true">✓</span> @endif
                            </button>
                        </form>
                        <div class="task-copy">
                            <h3>{{ $task->title }}</h3>
                            @if ($task->notes)<p>{{ $task->notes }}</p>@endif
                            @if ($task->due_date)
                                <span class="task-due {{ $task->status === 'pending' && $task->due_date->isBefore(today()) ? 'is-overdue' : '' }}">
                                    <span aria-hidden="true">{{ $task->status === 'completed' ? '✓' : '♡' }}</span>
                                    {{ $task->due_date->format('M j') }}
                                    @if ($task->status === 'pending' && $task->due_date->isBefore(today())) <span>· overdue</span> @endif
                                </span>
                            @endif
                        </div>
                        <div class="task-actions">
                            <a class="text-action" href="{{ route('tasks.edit', $task, false) }}">Edit</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task, false) }}" onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-action delete-action" type="submit">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <span class="empty-flower" aria-hidden="true">✿</span>
                        <h3>{{ $filter === 'completed' ? 'No finished tasks just yet' : ($filter === 'pending' ? 'Your to-do list is clear' : 'Your list is waiting for you') }}</h3>
                        <p>{{ $filter === 'completed' ? 'The little wins will show up here.' : 'Add one small thing you would like to get done.' }}</p>
                        @if ($filter !== 'completed')<a class="button button-soft" href="{{ route('tasks.create', [], false) }}">Add your first task</a>@endif
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="quick-add-panel" aria-labelledby="quick-add-heading">
            <div class="quick-add-heading">
                <span class="tiny-flower" aria-hidden="true">✿</span>
                <p class="eyebrow">MAKE A LITTLE PLAN</p>
                <h2 id="quick-add-heading">Add a task</h2>
                <p>Get it out of your head and onto the list.</p>
            </div>
            <form method="POST" action="{{ route('tasks.store', [], false) }}" class="task-form">
                @csrf
                <label for="title">Task name</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" maxlength="120" placeholder="What would you like to do?" required>
                @error('title')<p class="field-error">{{ $message }}</p>@enderror

                <label for="notes">A few details <span>(optional)</span></label>
                <textarea id="notes" name="notes" rows="3" maxlength="1200" placeholder="Add a little note...">{{ old('notes') }}</textarea>
                @error('notes')<p class="field-error">{{ $message }}</p>@enderror

                <label for="due_date">Due date <span>(optional)</span></label>
                <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}">
                @error('due_date')<p class="field-error">{{ $message }}</p>@enderror

                <button class="button button-primary submit-task" type="submit"><span aria-hidden="true">＋</span> Save to my list</button>
            </form>
            <p class="quick-note"><span aria-hidden="true">✧</span> Small steps count, too.</p>
        </aside>
    </div>
@endsection