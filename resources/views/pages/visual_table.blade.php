@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Visual Cədvəl</h3>
        
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-700 uppercase">Anbar</th>
                            @foreach($products as $product)
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-center text-sm font-semibold text-gray-700 uppercase">{{ $product->name }} {{ $product->code ? '- ' . $product->code : '' }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warehouses as $warehouse)
                            <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                                <td class="px-6 py-4 border-b border-gray-200 text-sm leading-5 font-medium text-gray-900">{{ $warehouse->name }}</td>
                                @foreach($products as $product)
                                    @php
                                        $quantity = $warehouse->product_entries->where('product_id', $product->id)->first()->quantity ?? 0;
                                    @endphp
                                    <td class="px-6 py-4 border-b border-gray-200 text-sm leading-5 text-center {{ $quantity > 0 ? 'text-green-600 font-bold' : 'text-red-600 font-semibold' }}">
                                        {{ $quantity }} ədəd
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection