<!-- Header -->
<header
    class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center ml-8">
        <button class="md:hidden text-gray-500 dark:text-gray-400">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h2 class="ml-4 text-xl font-semibold text-gray-800 dark:text-gray-200"><?= $title ?? 'Hierno' ?></h2>
    </div>
    <div class="flex items-center space-x-4">
   
        <button onclick="toggleTheme()"
            class="self-end p-2 dark:text-white bg-white dark:bg-gray-700  rounded-full top-2 md:top-5 right-3 md:right-20 hover:bg-gray-100 dark:hover:bg-gray-700 transition-transform transform hover:scale-110 z-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path id="theme-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
        </button>

    </div>
</header>