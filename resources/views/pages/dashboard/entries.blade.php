@extends('layouts.master')

@section('body')
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Girişlər</h3>
        <a href="{{ route('dashboard.create') }}"
            class="flex items-center justify-center px-4 py-2 bg-indigo-500 text-white rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"
                height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Mədaxil
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
            class="w-full mr-2 px-4 py-2 rounded-md focus:outline-none {{ request()->routeIs('dashboard.exits') ? 'bg-gradient-to-r from-green-500 to-green-700 text-white shadow-lg border-b-4 border-green-800' : 'bg-green-500 text-white hover:bg-green-600 hover:shadow-md' }}">

            Çıxışlar
        </a>
        <a href="{{ route('dashboard.overall') }}"
            class="w-full px-4 py-2 rounded-md focus:outline-none {{ request()->routeIs('dashboard.overall') ? 'bg-gradient-to-r from-purple-500 to-purple-700 text-white shadow-lg border-b-4 border-purple-800' : 'bg-purple-500 text-white hover:bg-purple-600 hover:shadow-md' }}">
            Ümumi
        </a>
    </div>
    <div class="flex flex-col mt-3">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div>
                <form action="{{ route('dashboard.entries') }}" method="get">
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
                                        value="{{ $product->id }}">{{ $product->name }} {{ $product->code ? '- ' . $product->code : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="company">Şirkət</label>
                            <select id="company" name="company_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Şirkət seçin</option>
                                @foreach ($companies as $company)
                                    <option {{ $company_id && $company_id == $company->id ? 'selected' : '' }}
                                        value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="category">Kateqoriya</label>
                            <select id="category" name="category_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Kateqoriya seçin</option>
                                @foreach ($categories as $category)
                                    <option {{ $category_id && $category_id == $category->id ? 'selected' : '' }}
                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="start_date">Giriş tarixindən</label>
                            <input value="{{ $start_date ? $start_date : '' }}" name="start_date" id="start_date" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="datetime-local">
                        </div>
                        <div>
                            <label class="text-gray-700" for="end_date">Giriş tarixinədək</label>
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
                                №
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Kod
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
                                Şirkət
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbardan
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Kateqoriya
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Giriş tarixi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($productEntries as $product)
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $loop->index + 1 }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->product_code ?? '' }}
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
                                    {{ $product->company_name ?? "-" }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->from_warehouse ? $product->from_warehouse : '-' }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->subcategory_name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->entry_date }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="grid my-2">
                    <form class="flex w-1/3 justify-self-end" id="exportForm">
                        <input type="hidden" name="export_type" value="all" id="export_type">
                        <button
                            class="mr-2 w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-md shadow-lg hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            type="button" onclick="setExportType(event, 'current')">Səhifəni çap et</button>
                        <button
                            class="w-full px-4 py-2 bg-green-500 border-2 border-green-500 text-white rounded-md hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-400"
                            type="button" onclick="setExportType(event, 'all')">Çap et</button>
                    </form>
                </div>
                <div class="flex items-center justify-center my-2">
                    @include('components.pagination', ['paginator' => $productEntries])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function setExportType(event, type) {
            event.preventDefault(); // Prevent the default form submission

            // Set the export type in the hidden input
            document.getElementById('export_type').value = type;

            // Construct the URL with query parameters
            const params = new URLSearchParams(window.location.search);
            params.set('export_type', type); // Update or add the export_type parameter

            // Create the full URL with parameters
            const actionUrl = `{{ route('export.entryProducts') }}?${params.toString()}`;

            // Redirect to the constructed URL to trigger the form submission
            window.location.href = actionUrl;
        }
    </script>
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
