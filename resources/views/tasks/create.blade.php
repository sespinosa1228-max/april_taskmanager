@extends('layouts.app')

@section('title', 'Add a task | Petal & Plans')

@section('content')
    <section class="form-page-heading">
        <a class="back-link" href="{{ route('tasks.index', [], false) }}">← Back to my tasks</a>
        <p class="eyebrow">A NOTE TO YOUR FUTURE SELF</p>
        <h1>Add a little task<span class="title-period">.</span></h1>
        <p class="welcome-copy">It feels good to have a plan.</p>
    </section>
    @include('tasks._form', ['task' => null, 'submitLabel' => 'Add to my list'])
@endsection