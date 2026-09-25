<section class="edit-form-panel">
    <form method="POST" action="{{ $task ? route('tasks.update', $task, false) : route('tasks.store', [], false) }}" class="task-form">
        @csrf
        @if ($task) @method('PUT') @endif

        <label for="title">Task name</label>
        <input id="title" name="title" type="text" value="{{ old('title', $task?->title) }}" maxlength="120" placeholder="What would you like to do?" required autofocus>
        @error('title')<p class="field-error">{{ $message }}</p>@enderror

        <label for="notes">A few details <span>(optional)</span></label>
        <textarea id="notes" name="notes" rows="4" maxlength="1200" placeholder="Add a little note...">{{ old('notes', $task?->notes) }}</textarea>
        @error('notes')<p class="field-error">{{ $message }}</p>@enderror

        <label for="due_date">Due date <span>(optional)</span></label>
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}">
        @error('due_date')<p class="field-error">{{ $message }}</p>@enderror

        @if ($task)
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="pending" @selected(old('status', $task->status) === 'pending')>To do</option>
                <option value="completed" @selected(old('status', $task->status) === 'completed')>Completed</option>
            </select>
            @error('status')<p class="field-error">{{ $message }}</p>@enderror
        @endif

        <div class="form-actions">
            <button class="button button-primary" type="submit">{{ $submitLabel }}</button>
            <a class="button button-quiet" href="{{ route('tasks.index', [], false) }}">Cancel</a>
        </div>
    </form>
</section>