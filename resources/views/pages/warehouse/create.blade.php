@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Yeni anbar yarat</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('warehouses.store') }}" method="post">
            @csrf
            <div class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="name">Anbarın adı</label>
                    <input name="name" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text">
                </div>
                <div>
                    <label class="text-gray-700" for="name">Anbardar</label>
                    <input name="warehouseman" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text">
                </div>
                <div class="flex items-center mt-2">
                    <label class="text-gray-700" for="is_main">Əsas anbar?</label>
                    <input type="checkbox" name="is_main" class="ml-2">
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat</button>
            </div>
        </form>
    </div>
@endsection