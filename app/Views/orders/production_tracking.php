<div class="max-w-4xl mx-auto mt-8">
    <div class="p-6 bg-white rounded-xl shadow-lg dark:bg-gray-800">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Suivi de production - Commande #<?= $order->reference ?>
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full 
                <?= $order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                   ($order->status === 'canceled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                   'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') ?>">
                <?= ucfirst($order->status) ?>
            </span>
        </div>

        <div class="space-y-6">
            <!-- Timeline des étapes -->
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
                            <a href="<?= PUBLIC_URL ?>production-step/edit/<?= $order->id ?>/<?= $step->id ?>"
                               class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded dark:bg-gray-700 dark:hover:bg-gray-600">
                                Modifier
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Actions -->
            <div class="flex justify-between pt-4 border-t dark:border-gray-700">
                <a href="<?= PUBLIC_URL ?>order/show/<?= $order->id ?>"
                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Retour à la commande
                </a>
                <a href="<?= PUBLIC_URL ?>production-step/create/<?= $order->id ?>"
                   class="px-4 py-2 text-white bg-blue-500 rounded-lg shadow-sm hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Ajouter une étape
                </a>
            </div>
        </div>
    </div>
</div>