@extends('layouts.master')

@section('body')
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Məhsullar</h3>
        <a href="{{ route('products.create') }}"
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
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full text-center">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Adı
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Açıqlaması
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Subanbar
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Yaranma tarixi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$product->name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$product->description}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$product->subcategory->name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$product->created_at}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex items-center justify-center my-2">
                    @include('components.pagination', ['paginator' => $products])
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
