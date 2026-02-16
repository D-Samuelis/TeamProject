<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Role Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-900 text-gray-100 font-sans min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="flex items-center gap-6 bg-gray-800 p-4 shadow-lg border-b-2 border-cyan-400">

        <!-- Main Dashboard Link -->
        <a href="{{ route('dashboard') }}"
            class="text-cyan-400 hover:text-cyan-100 hover:drop-shadow-lg transition duration-200 font-semibold">
            Dashboard
        </a>

        <!-- Role-specific Links -->
        @role('client')
            <span class="text-gray-400">|</span>
            <a href="/client"
                class="text-cyan-400 hover:text-cyan-100 hover:drop-shadow-lg transition duration-200 font-semibold">
                Client Area
            </a>
        @endrole

        @role('provider')
            <span class="text-gray-400">|</span>
            <a href="/provider"
                class="text-cyan-400 hover:text-cyan-100 hover:drop-shadow-lg transition duration-200 font-semibold">
                Provider Area
            </a>
        @endrole

        @role('admin')
            <span class="text-gray-400">|</span>
            <a href="/admin"
                class="text-cyan-400 hover:text-cyan-100 hover:drop-shadow-lg transition duration-200 font-semibold">
                Admin Panel
            </a>
            <a href="/audio"
                class="text-cyan-400 hover:text-cyan-100 hover:drop-shadow-lg transition duration-200 font-semibold">
                Audio Test
            </a>
        @endrole

        <!-- Right side: User info + Logout -->
        <div class="ml-auto flex items-center gap-4">
            @auth
                <span class="text-gray-400 text-sm">Logged in as: {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="px-3 py-1 text-sm bg-red-500 hover:bg-red-600 text-white rounded transition duration-200">
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <span class="text-gray-400 text-sm">Not logged in</span>
            @endguest
        </div>

    </nav>


    <!-- Page Content -->
    <main class="grow flex items-center justify-center">
        @yield('content')
    </main>

</body>

</html>
