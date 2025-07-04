@extends('layouts.master')

@section('body')
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Şassilər</h3>
        <a href="{{ route('highways.create') }}"
            class="flex items-center justify-center px-4 py-2 bg-indigo-500 text-white rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"
                height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Yenisini yarat
        </a>
    </div>
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div>
                <form action="{{ route('highways.index') }}" method="get">
                    <div class="grid grid-cols-3 mt-4 gap-3 mb-4">
                        <div>
                            <label class="text-gray-700" for="warehouse">Şassi nömrəsi</label>
                            <select id="highway_code" name="highway_code"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option selected value="">Şassi nömrəsi seçin</option>
                                @foreach ($codes as $code)
                                    <option
                                        value="{{ $code->code }}">{{ $code->code }}</option>
                                @endforeach
                            </select>
                            {{-- <input value="{{ $highway_code ? $highway_code : '' }}" name="highway_code" id="highway_code" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text"> --}}
                        </div>
                        <div>
                            <label class="text-gray-700" for="from_warehouse">Anbardan</label>
                            <select id="from_warehouse" name="from_warehouse_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option selected value="">Anbar seçin</option>
                                @foreach ($warehouses as $warehouse)
                                    <option {{ $from_warehouse_id && $from_warehouse_id == $warehouse->id ? 'selected' : '' }} value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div>
                            <label class="text-gray-700" for="product">Məhsul</label>
                            <select id="product" name="product_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Məhsul seçin</option>
                                @foreach ($products as $product)
                                    <option {{ $product_id && $product_id == $product->id ? 'selected' : '' }}
                                        value="{{ $product->id }}">{{ $product->name }} {{ $product->code ? '- ' . $product->code : '' }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="self-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-green-500 border-2 border-green-500 text-white rounded-md hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">Axtar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full text-center">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Nömrəsi
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Cari anbar
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Daha çox
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Yerdəyişmə
                            </th>
                            {{-- <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Məhsul
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Sayı
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                PDF faylı
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tarix
                            </th> --}}
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($highways as $highway)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$highway->code}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$highway->warehouse_name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <a href="{{ route('highways.show', ['highway' => $highway->id]) }}">Ətraflı</a>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <a href="{{ route('highways.changeWarehousePage', ['highway' => $highway->id]) }}">Düzəliş et</a>
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$highway->product_name}} {{ $highway->product_code ? '- ' . $highway->product_code : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$highway->quantity}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$highway->warehouse_name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    @if($highway->pdf_file)
                                        <a class="text-indigo-400 underline" href="{{ asset('storage/' . $highway->pdf_file) }}" target="_blank">Göstər</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$highway->entry_date}}
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex items-center justify-center my-2">
                    @include('components.pagination', ['paginator' => $highways])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>

    $(document).ready(function() {
        $('#highway_code').select2();
        const selectedHighways = @json($highway_codes);
        $('#highway_code').val(selectedHighways).trigger('change');
    });

    </script>
@endsection

@if (session('success'))
    @section('script')
        <script>
            toastr.options = {
                "progressBar": true,
                "closeButton": true
            }
            toastr.success("{{session('success')}}", "Uğurlu!");
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
            toastr.error("{{session('error')}}", "Xəta!");
        </script>
    @endsection
@endif
