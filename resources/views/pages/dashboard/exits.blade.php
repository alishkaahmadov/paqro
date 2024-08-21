@extends('layouts.master')

@section('body')
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Çıxışlar</h3>
        <a href="{{ route('dashboard.transferPage') }}"
            class="flex items-center justify-center px-4 py-2 bg-indigo-500 text-white rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"
                height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Yeni transfer əlavə et
        </a>
    </div>
    <div class="w-full mt-4">
        <a href="{{ route('dashboard.index') }}"
            class="w-full mr-2 px-4 py-2 rounded-md focus:outline-none {{ request()->routeIs('dashboard.index') ? 'bg-gradient-to-r from-yellow-500 to-yellow-700 text-white shadow-lg border-b-4 border-yellow-800' : 'bg-yellow-500 text-white hover:bg-yellow-600 hover:shadow-md' }}">
            Əsas
        </a>
        <a href="{{ route('dashboard.entries') }}"
            class="w-full mr-2 px-4 py-2 rounded-md focus:outline-none {{ request()->routeIs('dashboard.entries') ? 'bg-gradient-to-r from-blue-500 to-blue-700 text-white shadow-lg border-b-4 border-blue-800' : 'bg-blue-500 text-white hover:bg-blue-600 hover:shadow-md' }}">

            Girişlər
        </a>
        <a href="{{ route('dashboard.exits') }}"
            class="w-full px-4 py-2 rounded-md focus:outline-none {{ request()->routeIs('dashboard.exits') ? 'bg-gradient-to-r from-green-500 to-green-700 text-white shadow-lg border-b-4 border-green-800' : 'bg-green-500 text-white hover:bg-green-600 hover:shadow-md' }}">

            Çıxışlar
        </a>
    </div>
    <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div>
                <form action="{{ route('dashboard.exits') }}" method="get">
                    <div class="grid grid-cols-3 mt-4 gap-3 mb-4">
                        <div>
                            <label class="text-gray-700" for="warehouse">Anbar</label>
                            <select id="warehouse" name="warehouse_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($warehouses as $warehouse)
                                    <option
                                        {{ $warehouse_id && $warehouse_id == $warehouse->id ? 'selected' : ($warehouse->id == 1 ? 'selected' : '') }}
                                        value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="product">Məhsul</label>
                            <select id="product" name="product_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Məhsul seçin</option>
                                @foreach ($products as $product)
                                    <option {{ $product_id && $product_id == $product->id ? 'selected' : '' }}
                                        value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="category">Subanbar</label>
                            <select id="category" name="category_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Subanbar seçin</option>
                                @foreach ($categories as $category)
                                    <option {{ $category_id && $category_id == $category->id ? 'selected' : '' }}
                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="highway_code">Şosse nömrəsi</label>
                            <input value="{{ $highway_code ? $highway_code : '' }}" name="highway_code" id="highway_code" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text">
                        </div>
                        <div>
                            <label class="text-gray-700" for="start_date">Çıxış tarixindən</label>
                            <input value="{{ $start_date ? $start_date : '' }}" name="start_date" id="start_date" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="datetime-local">
                        </div>
                        <div>
                            <label class="text-gray-700" for="end_date">Çıxış tarixinədək</label>
                            <input value="{{ $end_date ? $end_date : '' }}" name="end_date" id="end_date" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="datetime-local">
                        </div>
                    </div>
                    <div class="flex justify-end mb-4">
                        <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Axtar</button>
                    </div>
                </form>
            </div>
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full text-center">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbar
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Məhsul
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Sayı
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbara
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Subanbar
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Şosse nömrəsi
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Çıxış tarixi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($productExits as $product)
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->from_warehouse }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->product_name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->quantity }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->to_warehouse ? $product->to_warehouse : '-' }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->subcategory_name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->highway_code ? $product->highway_code : '-' }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->exit_date }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex items-center justify-center my-2">
                    @include('components.pagination', ['paginator' => $productExits])
                </div>
            </div>
        </div>
    </div>
@endsection

@if (session('success'))
    @section('script')
        <script>
            toastr.options = {
                "progressBar": true,
                "closeButton": true
            }
            toastr.success("{{ session('success') }}", "Uğurlu!");
        </script>
    @endsection
@endif
@if (session('error'))
    @section('script')
        <script>
            toastr.options = {
                "progressBar": true,
                "closeButton": true
            }
            toastr.error("{{ session('error') }}", "Xəta!");
        </script>
    @endsection
@endif
