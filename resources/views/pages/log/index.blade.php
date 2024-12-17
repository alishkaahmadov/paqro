@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Loglar</h3>
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full text-center">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                İstifadəçi
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Əməliyyat
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Əməliyyat adı
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Məlumat
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tarix
                            </th>
                        </tr>
                    </thead>
    
                    <tbody class="bg-white">
                        @foreach ($logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$log->user->email}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$log->action}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{json_decode($log->changes)->action}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{json_encode(json_decode($log->changes)->data ?? '', JSON_UNESCAPED_UNICODE)}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$log->created_at}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex items-center justify-center my-2">
                    @include('components.pagination', ['paginator' => $logs])
                </div>
            </div>
        </div>
    </div>
    
@endsection