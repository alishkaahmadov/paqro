<div x-cloak :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
    class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

<div x-cloak :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
    class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
    <div class="flex items-center justify-center mt-8">
        <div class="flex items-center">
            
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="" class="w-10 h-10">
            <span class="mx-2 text-2xl font-semibold text-white">P-Aqro MMC</span>
        </div>
    </div>

    <nav class="mt-10">
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'dashboard' ? 'bg-gray-700' : ''); ?>"
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
        
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'warehouses' ? 'bg-gray-700' : ''); ?>"
                href="/warehouses">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Anbarlar</span>
            </a>
        
        
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'users' ? 'bg-gray-700' : ''); ?>"
                href="/users">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">İstifadəçilər</span>
            </a>
        
        <?php if(Auth::user()->is_admin): ?>
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'logs' ? 'bg-gray-700' : ''); ?>"
                href="/logs">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Loglar</span>
            </a>
        <?php endif; ?>
        
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'products' ? 'bg-gray-700' : ''); ?>"
                href="/products">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <path d="M16 8h2v2h-2zM6 8h2v2H6zM10 8h4v2h-4z"></path>
                </svg>

                <span class="mx-3">Məhsullar</span>
            </a>
        
        
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'highways' ? 'bg-gray-700' : ''); ?>"
            href="/highways">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 2h-3a1 1 0 0 0-1 1v18a1 1 0 0 0 1 1h3"></path>
                <path d="M6 2h3a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1h-3"></path>
                <line x1="9" y1="12" x2="15" y2="12"></line>
            </svg>
            <span class="mx-3">Şassilər</span>
        </a>
        
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'visual-table' ? 'bg-gray-700' : ''); ?>"
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
        <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'limits' ? 'bg-gray-700' : ''); ?>"
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
        <?php if(Auth::user()->is_admin): ?>
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'product-categories' ? 'bg-gray-700' : ''); ?>"
                href="/product-categories">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Kateqoriyanı dəyiş</span>
            </a>
            <a class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-opacity-25 <?php echo e(Route::current()->uri() == 'import-excel' ? 'bg-gray-700' : ''); ?>"
                href="/import-excel">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5L12 4l9 5.5v9.5a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9.5z"></path>
                    <path d="M9 22v-6h6v6"></path>
                </svg>

                <span class="mx-3">Exceldən inteqrasiya</span>
            </a>
        <?php endif; ?>
    </nav>
</div>
<?php /**PATH C:\Users\User\Desktop\Alishka Projects\paqro\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>