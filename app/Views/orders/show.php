<!-- views/orders/show.php -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <!-- En-tête de la commande -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Commande #<?= $order->reference ?>
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Client: <?= $order->customer->name ?>
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                    <?= $order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                       ($order->status === 'canceled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                       'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') ?>">
                    <?= ucfirst($order->status) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation par onglets -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <ul class="flex flex-wrap -mb-px">
            <li class="mr-2">
                <a href="<?= $this->basePath ?>/order/show/<?= $order->id ?>"
                   class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 <?= !strpos($_SERVER['REQUEST_URI'], 'production') ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : '' ?>">
                    Détails
                </a>
            </li>
            <li class="mr-2">
                <a href="<?= $this->basePath ?>/order/<?= $order->id ?>/production"
                   class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 <?= strpos($_SERVER['REQUEST_URI'], 'production') !== false ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : '' ?>">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Suivi Production
                    </div>
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenu conditionnel selon l'onglet -->
    <?php if (!strpos($_SERVER['REQUEST_URI'], 'production')): ?>
        <!-- Détails de la commande -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Informations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Détails</h3>
                    <ul class="space-y-2">
                        <li class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Date création:</span>
                            <span><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Livraison prévue:</span>
                            <span><?= date('d/m/Y', strtotime($order->delivery_date)) ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Priorité:</span>
                            <span class="capitalize"><?= $order->priority ?></span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Client</h3>
                    <ul class="space-y-2">
                        <li class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Nom:</span>
                            <span><?= $order->customer->name ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Email:</span>
                            <span><?= $order->customer->email ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Téléphone:</span>
                            <span><?= $order->customer->phone ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <?php if ($order->notes): ?>
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</h3>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-gray-800 dark:text-gray-200"><?= nl2br(htmlspecialchars($order->notes)) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Fichiers associés -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Fichiers</h2>
                <a href="<?= $this->basePath ?>/file/upload/<?= $order->id ?>"
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Ajouter un fichier
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Taille</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        <?php foreach ($order->files as $file): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                <a href="<?= $this->basePath ?>/uploads/<?= $file->file_path ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                    <?= $file->file_name ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= $file->file_type ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= round($file->file_size / 1024, 2) ?> KB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <a href="<?= $this->basePath ?>/file/delete/<?= $file->id ?>" class="text-red-500 hover:text-red-700">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- Suivi de production -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Suivi de production</h2>
                <a href="<?= $this->basePath ?>/production-step/create/<?= $order->id ?>"
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Ajouter une étape
                </a>
            </div>

            <div class="relative">
                <!-- Ligne de timeline -->
                <div class="absolute left-4 top-0 h-full w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                
                <?php foreach ($order->productionSteps as $index => $step): ?>
                <div class="relative mb-8 pl-12">
                    <!-- Point de timeline -->
                    <div class="absolute left-0 flex items-center justify-center w-8 h-8 rounded-full 
                        <?= $step->status === 'completed' ? 'bg-green-500' : 
                           ($step->status === 'in_progress' ? 'bg-blue-500' : 
                           ($step->status === 'failed' ? 'bg-red-500' : 'bg-gray-300 dark:bg-gray-600')) ?>">
                        <span class="text-white font-bold"><?= $index + 1 ?></span>
                    </div>
                    
                    <!-- Carte d'étape -->
                    <div class="p-4 border rounded-lg shadow-sm 
                        <?= $step->status === 'completed' ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-gray-700' : 
                           ($step->status === 'in_progress' ? 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-gray-700' : 
                           ($step->status === 'failed' ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-700')) ?>">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-lg capitalize"><?= $step->step ?></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Statut: <?= $step->status ?>
                                </p>
                                <?php if ($step->assigned_to): ?>
                                <p class="text-sm mt-1">
                                    <span class="font-medium">Responsable:</span> <?= $step->assigned_to ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <?php if ($step->start_time): ?>
                                <p class="text-xs text-gray-500">
                                    Début: <?= date('d/m/Y H:i', strtotime($step->start_time)) ?>
                                </p>
                                <?php endif; ?>
                                <?php if ($step->end_time): ?>
                                <p class="text-xs text-gray-500">
                                    Fin: <?= date('d/m/Y H:i', strtotime($step->end_time)) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($step->comments): ?>
                        <div class="mt-2 p-2 bg-white dark:bg-gray-800 rounded border dark:border-gray-700">
                            <p class="text-sm"><?= $step->comments ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-3 flex space-x-2">
                            <a href="<?= $this->basePath ?>/production-step/edit/<?= $order->id ?>/<?= $step->id ?>"
                               class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded dark:bg-gray-700 dark:hover:bg-gray-600">
                                Modifier
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="flex justify-between pt-4">
        <a href="<?= $this->basePath ?>/order"
           class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
            Retour aux commandes
        </a>
        <div class="space-x-2">
            <a href="<?= $this->basePath ?>/order/edit/<?= $order->id ?>"
               class="px-4 py-2 text-white bg-blue-500 rounded-lg shadow-sm hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                Modifier
            </a>
        </div>
    </div>
</div>