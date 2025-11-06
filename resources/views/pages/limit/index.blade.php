@extends('layouts.master')

@section('body')
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Limitlər</h3>
    </div>
    <div class="flex flex-col mt-3">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div>
                <form action="{{ route('limit.index') }}" method="get">
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
                                <option value="all" {{ $warehouse_id && $warehouse_id == "all" ? 'selected' : '' }}>Bütün anbarlar</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="text-gray-700" for="product">Məhsul</label>
                            <select id="product" name="product_ids[]" multiple
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($products as $product)
                                    <option
                                        value="{{ $product->id }}">{{ $product->name }}
                                        {{ $product->code ? '- ' . $product->code : '' }}</option>
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
                            <label class="text-gray-700" for="except_category">Kateqoriyadan savayı</label>
                            <select id="except_category" name="except_category_ids[]" multiple
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="color">Rəng</label>
                            <select id="color" name="color"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Rəng seçin</option>
                                    <option {{ $color && $color == 'red' ? 'selected' : '' }} value="red">Qırmızı</option>
                                    <option {{ $color && $color == 'yellow' ? 'selected' : '' }} value="yellow">Sarı</option>
                                    <option {{ $color && $color == 'white' ? 'selected' : '' }} value="white">Ağ</option>
                            </select>
                        </div>
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
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                №
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbar
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
                                Qalıq
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Ölçü vahidi
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Kateqoriya
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Limit
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                İllik təlabat
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Təyinetmə
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($productEntries as $product)
                            <tr class="{{ $product->is_ordered ? 'bg-yellow-400' : ($product->limit >= $product->quantity && $product->limit != 0 ? 'bg-red-400' : '') }}">
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ ($productEntries->currentPage() - 1) * 50 + 1 + $loop->index }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->warehouse_name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->product->code ? $product->product->code : '' }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->product->name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{  $product->quantity }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->measure }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->subcategory->name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->limit ?? 0 }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{ $product->demand ?? 0 }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <a href="{{ route('limit.edit', [
                                        'entry' => $product->id, 
                                        'warehouse_id' => request('warehouse_id'),
                                        'category_id' => request('category_id')
                                    ]) }}">Təyin et</a>
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
                            type="button" onclick="printToExcel(event)">Çap et (Excel)</button>
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
        @if (session('success'))
            toastr.options = {
                "progressBar": true,
                "closeButton": true
            }
            toastr.success("{{session('success')}}", "Uğurlu!");
        @endif
        @if (session('error'))
            toastr.options = {
                "progressBar": true,
                "closeButton": true
            }
            toastr.error("{{session('error')}}", "Xəta!");
        @endif

        $(document).ready(function() {
            $('#product').select2();
            const selectedProductIds = @json($product_ids);
            $('#product').val(selectedProductIds).trigger('change');

            $('#except_category').select2();
            const selectedExceptCategoryIds = @json($except_category_ids);
            $('#except_category').val(selectedExceptCategoryIds).trigger('change');
        });

        function printToExcel(event) {
            event.preventDefault();
            const params = new URLSearchParams(window.location.search);
            const actionUrl = `{{ route('export.limitExcel') }}?${params.toString()}`;
            window.location.href = actionUrl;
        }

    </script>
@endsection