@extends('web.layouts.app')

@section('title', 'Bexora | Edit Category')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">

    {{-- Back link --}}
    <a href="{{ route('admin.categories.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Back to categories
    </a>

    <div class="bg-white border border-gray-200 rounded-xl p-8">

        {{-- Header with meta info --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 mb-1">Edit Category</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">/{{ $category->slug }}</span>
                    <span>·</span>
                    <span>{{ $category->services_count }} {{ Str::plural('service', $category->services_count) }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Name <span class="text-red-400">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    autofocus
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400
                           focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition
                           @error('name') border-red-400 focus:ring-red-400 @enderror"
                >
                @error('name')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-400">Changing the name will also regenerate the slug.</p>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Description
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    maxlength="500"
                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400
                           focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-none
                           @error('description') border-red-400 focus:ring-red-400 @enderror"
                >{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-2">

                {{-- Delete --}}
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                      onsubmit="return confirm('Delete \'{{ $category->name }}\'? Services in this category will become uncategorised.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-sm text-red-400 hover:text-red-600 transition font-medium">
                        Delete category
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.categories.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-900 transition px-4 py-2">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-gray-900 text-white text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-gray-700 transition">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection