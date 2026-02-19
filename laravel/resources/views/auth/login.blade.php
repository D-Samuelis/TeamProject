@extends('layouts.app')

@section('content')
    <div class="card bg-gray-800 p-8 rounded-xl shadow-lg max-w-md mx-auto mt-16">
        <h1 class="text-4xl font-extrabold text-cyan-400 text-center mb-6 drop-shadow-lg">Login</h1>

        @if ($errors->any())
            <div class="form-errors mb-4 p-4 bg-red-900 rounded-lg border border-red-500">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-red-400">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="form-group flex flex-col">
                <label for="email" class="text-cyan-400 font-semibold mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="bg-gray-900 text-gray-100 p-3 rounded-lg border-2 border-cyan-400 focus:border-cyan-100 focus:ring focus:ring-cyan-400 focus:ring-opacity-50 outline-none transition">
            </div>

            <div class="form-group flex flex-col">
                <label for="password" class="text-cyan-400 font-semibold mb-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="bg-gray-900 text-gray-100 p-3 rounded-lg border-2 border-cyan-400 focus:border-cyan-100 focus:ring focus:ring-cyan-400 focus:ring-opacity-50 outline-none transition">
            </div>

            <div class="form-actions flex justify-between items-center mt-6">
                <button type="submit"
                    class="px-6 py-3 bg-cyan-100 hover:bg-cyan-400 text-gray-900 font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200 cursor-pointer">
                    Login
                </button>
                <a href="{{ route('register') }}"
                    class="text-cyan-400 hover:text-cyan-100 font-semibold transition duration-200 cursor-pointer">
                    Register
                </a>
            </div>
        </form>
    </div>
@endsection
