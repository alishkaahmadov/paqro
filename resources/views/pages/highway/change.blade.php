@extends('layouts.master')

@section('body')
<div class="container mx-auto">
    <h2 class="text-xl font-bold mb-4">Yerdəyişmə</h2>

    <form action="{{ route('highways.changeWarehouse', $highwayId) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="code" class="block text-sm font-medium text-gray-700">Şassi nömrəsi</label>
            <input required type="text" id="code" name="code" value="{{ old('code', $code) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

            <label for="current_warehouse" class="block text-sm font-medium text-gray-700">Cari anbar</label>
            <select id="current_warehouse" name="current_warehouse"
                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                @foreach ($warehouses as $warehouse)
                    <option {{ $current_warehouse == $warehouse->id ? 'selected' :  '' }} value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
            @error('current_warehouse')
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
