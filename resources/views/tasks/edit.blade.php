@extends('layouts.app')

@section('title', 'Edit task | Petal & Plans')

@section('content')
    <section class="form-page-heading">
        <a class="back-link" href="{{ route('tasks.index', [], false) }}">← Back to my tasks</a>
        <p class="eyebrow">A LITTLE UPDATE</p>
        <h1>Edit your task<span class="title-period">.</span></h1>
        <p class="welcome-copy">Plans can change. Your list can, too.</p>
    </section>
    @include('tasks._form', ['task' => $task, 'submitLabel' => 'Save changes'])
@endsection