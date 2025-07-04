@extends('layouts.master')

@section('body')
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">İstifadəçilər</h3>
        <div class="flex">
            <a href="{{ route('users.edit', Auth::user()->id) }}"
                class="flex mr-2 items-center justify-center px-4 py-2 bg-indigo-500 text-white rounded-lg">
                Düzəliş et
            </a>
            <a href="{{ route('users.create') }}"
                class="flex items-center justify-center px-4 py-2 bg-indigo-500 text-white rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"
                    height="14">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Yenisini yarat
            </a>
        </div>
    </div>
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full text-center">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Adı
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tip
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Yaranma tarixi
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tənzimlə
                            </th>
                        </tr>
                    </thead>
    
                    <tbody class="bg-white">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$user->name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$user->email}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$user->is_admin ? "Admin" : "User"}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$user->is_active ? 'Aktiv' : 'Deaktiv'}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$user->created_at}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5">
                                    @if ($user->is_active)
                                        @if ($user->email != "admin@gmail.com")
                                            <form action="{{ route('users.deactivate', $user->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" onclick="return confirm('Deaktiv etmək istədiyinizdən əminsiniz?')" 
                                                        class="text-red-500 hover:text-red-700">
                                                    Deaktiv et
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <form action="{{ route('users.activate', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('Aktiv etmək istədiyinizdən əminsiniz?')" 
                                                    class="text-red-500 hover:text-red-700">
                                                Aktiv et
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex items-center justify-center my-2">
                    @include('components.pagination', ['paginator' => $users])
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
