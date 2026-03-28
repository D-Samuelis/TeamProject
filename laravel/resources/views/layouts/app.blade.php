<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Home')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Font Awesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Material Icons (Google Fonts) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Lineicons CSS -->
    <link rel="stylesheet" href="https://cdn.lineicons.com/3.0/lineicons.css">

    <!-- Markdown parser -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<nav class="nav-main">
    @include('partials.nav')
</nav>

<main class="main">
    @yield('content')
</main>

<footer>
    @include('partials.footer')
</footer>

@include('partials.overlays')

</body>

</html>
