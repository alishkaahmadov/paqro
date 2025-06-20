@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Exceldən inteqrasiya</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('import-excel') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="file">Excel file</label>
                    <input type="file" required name="file" accept=".xlsx,.csv" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat</button>
            </div>
        </form>
    </div>
@endsection