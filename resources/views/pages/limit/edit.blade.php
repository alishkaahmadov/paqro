@extends('layouts.master')

@section('body')
<div class="container mx-auto">
    <h2 class="text-xl font-bold mb-4">Limit düzəlişi</h2>

    <form action="{{ route('limit.update', $id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="limit" class="block text-sm font-medium text-gray-700">Limit</label>
            <input type="text" id="limit" name="limit" value="{{ old('limit', $limit) }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('limit')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="is_ordered" class="block text-sm font-medium text-gray-700">Sifariş olunub?</label>
            <input type="checkbox" id="is_ordered" name="is_ordered" value="true" {{ old('is_ordered', $is_ordered) ? 'checked' : '' }}>
        </div>
        <input type="hidden" name="warehouse_id" value="{{ request('warehouse_id') }}">
        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
        <div class="mt-6">
            <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-bold rounded hover:bg-blue-700">
                Dəyiş
            </button>
        </div>
    </form>
</div>
@endsection
