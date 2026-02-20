@extends('layouts.app')

@section('title', 'Welcome to RoleTest')

@section('content')
<div class="text-center px-4">
    <!-- Main Title -->
    <h1 class="text-5xl md:text-6xl font-bold text-cyan-400 drop-shadow-lg mb-6">
        Welcome to RoleTest
    </h1>

    <!-- Description -->
    <p class="text-gray-300 text-lg md:text-xl mb-8 max-w-xl mx-auto drop-shadow-sm">
        A simple Laravel demo to showcase role-based access control.  
        Access dashboards based on your role: client, provider, or admin.
    </p>

    <!-- Buttons -->
    @guest
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="{{ route('register') }}"
           class="px-6 py-3 bg-cyan-400 hover:bg-cyan-100 text-gray-900 font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200">
            Register
        </a>
        <a href="{{ route('login') }}"
           class="px-6 py-3 bg-cyan-400 hover:bg-cyan-100 text-gray-900 font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200">
            Login
        </a>
    </div>
    @endguest

    @auth
    <p class="text-gray-400">You are already logged in.</p>
    @endauth
</div>
@endsection
