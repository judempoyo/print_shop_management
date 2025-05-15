<!-- Contenu principal -->
<div class="flex flex-col flex-1 overflow-hidden">
    <!-- Main content -->
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <?= date('l, j F Y') ?>
            </div>
        </div>


        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total clients -->
            <div class="bg-white p-6 rounded-xl shadow dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total clients</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                            <?= $totalCustomers ?>
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>

       
    </main>
</div>

