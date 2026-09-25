<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks');

Route::patch('/tasks/{task}/status', [TaskController::class, 'toggleStatus'])
    ->name('tasks.status.update');

Route::resource('tasks', TaskController::class)->except('show');
