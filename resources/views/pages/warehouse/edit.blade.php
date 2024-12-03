@extends('layouts.master')

@section('body')
<div class="container mx-auto">
    <h2 class="text-xl font-bold mb-4">Anbar düzəlişi</h2>

    <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Adı</label>
            <input type="text" id="name" name="name" value="{{ old('name', $warehouse->name) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="warehouseman" class="block text-sm font-medium text-gray-700">Anbardar</label>
            <input type="text" id="warehouseman" name="warehouseman" value="{{ old('warehouseman', $warehouse->warehouseman) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('warehouseman')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="is_main" class="block text-sm font-medium text-gray-700">Əsas anbar</label>
            <select id="is_main" name="is_main"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="1" {{ $warehouse->is_main ? 'selected' : '' }}>Bəli</option>
                <option value="0" {{ !$warehouse->is_main ? 'selected' : '' }}>Xeyr</option>
            </select>
            @error('is_main')
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
