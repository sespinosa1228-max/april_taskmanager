<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Petal & Plans'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('tasks.index', [], false) }}" aria-label="Petal and Plans home">
                <span class="brand-mark" aria-hidden="true">✿</span>
                <span class="brand-name">petal<span>&</span>plans</span>
            </a>

            <p class="sidebar-label">YOUR LITTLE SPACE</p>
            <nav class="side-nav" aria-label="Main navigation">
                <a class="side-link {{ request()->routeIs('tasks.index') ? 'is-active' : '' }}" href="{{ route('tasks.index', [], false) }}">
                    <span class="nav-icon" aria-hidden="true">⌂</span> My tasks
                    <span class="nav-count">{{ $pendingTaskCount }}</span>
                </a>
                <a class="side-link {{ request()->routeIs('tasks.create') ? 'is-active' : '' }}" href="{{ route('tasks.create', [], false) }}">
                    <span class="nav-icon" aria-hidden="true">＋</span> Add a task
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="room-art" aria-hidden="true">
                    <span class="art-sparkle sparkle-one">✦</span>
                    <span class="art-sparkle sparkle-two">✧</span>
                    <span class="art-flower">✿</span>
                    <span class="art-caption">a little progress<br>is still progress</span>
                </div>
                <p class="sidebar-footnote">Your day, at your pace.</p>
            </div>
        </aside>

        <div class="main-column">
            <header class="topbar">
                <span class="topbar-date">{{ now()->format('l, F j') }}</span>
                <div class="topbar-user"><span class="user-dot">✿</span><span>My dashboard</span></div>
            </header>

            <main class="page-content">
                @if (session('success'))
                    <div class="flash-message" role="status">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>