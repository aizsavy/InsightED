<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'INSIGHTED - Student Personality Dashboard')</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="main-layout bg-gray-100">
    <div class="main-layout">
        @include('components.sidebar')

        <main class="dashboard-container pattern-wavy">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
