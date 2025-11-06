<div x-cloak :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
    class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

<div x-cloak :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
    class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
    <div class="flex items-center justify-center mt-8">
        <div class="flex items-center">
            {{-- <svg class="w-12 h-12" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M364.61 390.213C304.625 450.196 207.37 450.196 147.386 390.213C117.394 360.22 102.398 320.911 102.398 281.6C102.398 242.291 117.394 202.981 147.386 172.989C147.386 230.4 153.6 281.6 230.4 307.2C230.4 256 256 102.4 294.4 76.7999C320 128 334.618 142.997 364.608 172.989C394.601 202.981 409.597 242.291 409.597 281.6C409.597 320.911 394.601 360.22 364.61 390.213Z"
                    fill="#4C51BF" stroke="#4C51BF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M201.694 387.105C231.686 417.098 280.312 417.098 310.305 387.105C325.301 372.109 332.8 352.456 332.8 332.8C332.8 313.144 325.301 293.491 310.305 278.495C295.309 263.498 288 256 275.2 230.4C256 243.2 243.201 320 243.201 345.6C201.694 345.6 179.2 332.8 179.2 332.8C179.2 352.456 186.698 372.109 201.694 387.105Z"
                    fill="white" />
            </svg> --}}
            <img src="{{ asset('images/logo.png') }}" alt="" class="w-10 h-10">
            <span class="mx-2 text-2xl font-semibold text-white">P-Aqro MMC</span>
        </div>
    </div>

    <nav class="mt-10">
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'dashboard' ? 'bg-gray-700' : '' }}"
            href="/dashboard">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            <span class="mx-3">Əsas səhifə</span>
        </a>
        {{-- @if (Auth::user()->is_admin) --}}
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'warehouses' ? 'bg-gray-700' : '' }}"
                href="/warehouses">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Anbarlar</span>
            </a>
        {{-- @endif --}}
        {{-- @if (Auth::user()->is_admin) --}}
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'users' ? 'bg-gray-700' : '' }}"
                href="/users">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">İstifadəçilər</span>
            </a>
        {{-- @endif --}}
        @if (Auth::user()->is_admin)
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'logs' ? 'bg-gray-700' : '' }}"
                href="/logs">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Loglar</span>
            </a>
        @endif
        {{-- @if (Auth::user()->is_admin) --}}
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'products' ? 'bg-gray-700' : '' }}"
                href="/products">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <path d="M16 8h2v2h-2zM6 8h2v2H6zM10 8h4v2h-4z"></path>
                </svg>

                <span class="mx-3">Məhsullar</span>
            </a>
        {{-- @endif --}}
        {{-- <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'warehouses' ? 'bg-gray-700' : '' }}"
            href="/warehouses">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                <path d="M9 22v-6h6v6"></path>
            </svg>

            <span class="mx-3">Anbarlar</span>
        </a>
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'categories' ? 'bg-gray-700' : '' }}"
            href="/categories">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>

            <span class="mx-3">Subanbarlar</span>
        </a>
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'companies' ? 'bg-gray-700' : '' }}"
            href="/companies">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 21v-13h20v13"></path>
                <path d="M2 10l10-7 10 7"></path>
                <path d="M6 21v-5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v5"></path>
            </svg>

            <span class="mx-3">Şirkətlər</span>
        </a> --}}
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'highways' ? 'bg-gray-700' : '' }}"
            href="/highways">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 2h-3a1 1 0 0 0-1 1v18a1 1 0 0 0 1 1h3"></path>
                <path d="M6 2h3a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1h-3"></path>
                <line x1="9" y1="12" x2="15" y2="12"></line>
            </svg>
            <span class="mx-3">Şassilər</span>
        </a>
        {{-- <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'dnns' ? 'bg-gray-700' : '' }}"
            href="/dnns">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="16" rx="2" ry="2"></rect>
                <line x1="7" y1="10" x2="17" y2="10"></line>
                <line x1="7" y1="14" x2="11" y2="14"></line>
                <line x1="13" y1="14" x2="17" y2="14"></line>
            </svg>
            <span class="mx-3">DNN</span>
        </a> --}}
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'visual-table' ? 'bg-gray-700' : '' }}"
            href="/visual-table">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="3" y1="15" x2="21" y2="15"></line>
                <line x1="9" y1="3" x2="9" y2="21"></line>
                <line x1="15" y1="3" x2="15" y2="21"></line>
            </svg>
            <span class="mx-3">Visual Cədvəl</span>
        </a>
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'limits' ? 'bg-gray-700' : '' }}"
            href="/limits">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="3" y1="15" x2="21" y2="15"></line>
                <line x1="9" y1="3" x2="9" y2="21"></line>
                <line x1="15" y1="3" x2="15" y2="21"></line>
            </svg>
            <span class="mx-3">Limitlər</span>
        </a>
        @if (Auth::user()->is_admin)
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'product-categories' ? 'bg-gray-700' : '' }}"
                href="/product-categories">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Kateqoriyanı dəyiş</span>
            </a>
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 {{ Route::current()->uri() == 'import-excel' ? 'bg-gray-700' : '' }}"
                href="/import-excel">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Exceldən inteqrasiya</span>
            </a>
        @endif
    </nav>
</div>
