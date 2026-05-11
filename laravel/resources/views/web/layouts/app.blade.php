<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

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


    <style>
        .alerts-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 1rem 2rem;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }

        .alert ul {
            margin: 0;
            padding-left: 1rem;
        }

        .alert--success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert--error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>

<body>

    <nav class="nav-main">
        @include('web.layouts.partials.nav')
    </nav>

    @if (session('success') || session('error') || $errors->any())
        <div class="alerts-wrapper">
            @if (session('success'))
                <div class="alert alert--success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert--error">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert--error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <main class="main">
        @yield('content')
    </main>


    <footer>
        @include('web.layouts.partials.footer')
    </footer>
    <script type="module" src="{{ Vite::asset('resources/js/components/footer/footer.js') }}"></script>

    @include('web.layouts.partials.overlays')
    @include('web.layouts.partials.chatbot')

</body>

</html>
