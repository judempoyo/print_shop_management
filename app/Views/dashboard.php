<!-- Contenu principal -->
<div class="flex flex-col flex-1 overflow-hidden">
    <!-- Main content -->
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de Bord - Imprimerie</h1>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <?= date('l, j F Y') ?>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total clients -->
            <div class="bg-white p-6 rounded-xl shadow dark:bg-gray-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Clients</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                            <?= $stats['total_customers'] ?>
                        </p>
                        <p class="text-xs mt-1 <?= $stats['customer_growth'] >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                            <?= $stats['customer_growth'] >= 0 ? '↑' : '↓' ?> 
                            <?= abs($stats['customer_growth']) ?>% ce mois
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Commandes en cours -->
            <div class="bg-white p-6 rounded-xl shadow dark:bg-gray-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Commandes actives</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                            <?= $stats['active_orders'] ?>
                        </p>
                        <p class="text-xs mt-1"><?= $stats['orders_this_week'] ?> cette semaine</p>
                    </div>
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Production -->
            <div class="bg-white p-6 rounded-xl shadow dark:bg-gray-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">En production</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                            <?= $stats['in_production'] ?>
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-teal-600 h-1.5 rounded-full" style="width: <?= $stats['production_progress'] ?>%"></div>
                        </div>
                    </div>
                    <div class="p-3 rounded-full bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Livraisons -->
            <div class="bg-white p-6 rounded-xl shadow dark:bg-gray-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">À livrer</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                            <?= $stats['to_deliver'] ?>
                        </p>
                        <p class="text-xs mt-1"><?= $stats['delivered_today'] ?> livrées aujourd'hui</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques et tableaux -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Commandes récentes -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Commandes récentes</h2>
                    <a href="<?= PUBLIC_URL ?>order" class="text-sm text-teal-500 hover:underline">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Livraison</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <?php foreach ($recent_orders as $order): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <a href="<?= PUBLIC_URL ?>order/show/<?= $order->id ?>" class="text-teal-500 hover:underline"><?= $order->reference ?></a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?= $order->customer->name ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        <?= $order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                           ($order->status === 'canceled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                           'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200') ?>">
                                        <?= ucfirst($order->status) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?= date('d/m/Y', strtotime($order->delivery_date)) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- État des stocks -->
            <div class="bg-white p-6 rounded-xl shadow dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">État des stocks</h2>
                    <a href="<?= PUBLIC_URL ?>material" class="text-sm text-teal-500 hover:underline">Gérer</a>
                </div>
                <div class="space-y-4">
                    <?php foreach ($low_stock_materials as $material): ?>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-300"><?= $material->name ?></span>
                            <span class="text-gray-500 dark:text-gray-400"><?= $material->stock_quantity ?> <?= $material->unit ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-<?= $material->stock_quantity < $material->min_stock_level ? 'red' : 'green' ?>-500 h-2 rounded-full" 
                                 style="width: <?= min(100, ($material->stock_quantity / ($material->min_stock_level * 2)) * 100) ?> %">
                            </div>
                        </div>
                        <?php if ($material->stock_quantity < $material->min_stock_level): ?>
                        <p class="text-xs text-red-500 mt-1">Stock critique - Réapprovisionnement nécessaire</p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="bg-white p-6 rounded-xl shadow dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Activité récente</h2>
            <div class="space-y-4">
                <?php foreach ($recent_activity as $activity): ?>
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full 
                            <?= $activity['type'] === 'order' ? 'bg-teal-100 text-teal-600 dark:bg-teal-900/30' : 
                               ($activity['type'] === 'file' ? 'bg-purple-100 text-purple-600 dark:bg-purple-900/30' : 
                               'bg-green-100 text-green-600 dark:bg-green-900/30') ?>">
                            <?php if ($activity['type'] === 'order'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <?php elseif ($activity['type'] === 'file'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $activity['title'] ?></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?= $activity['description'] ?></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= $activity['time'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>