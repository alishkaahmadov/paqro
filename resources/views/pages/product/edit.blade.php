@extends('layouts.master')

@section('body')
<div class="container mx-auto">
    <h2 class="text-xl font-bold mb-4">Məhsul düzəlişi</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Adı</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="code" class="block text-sm font-medium text-gray-700">Kodu</label>
            <input type="text" id="code" name="code" value="{{ old('code', $product->code) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('code')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-bold rounded hover:bg-blue-700">
                Dəyiş
            </button>
        </div>
    </form>
</div>
@endsection
