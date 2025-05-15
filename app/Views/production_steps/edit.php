<div class="max-w-2xl mx-auto mt-8">
    <div class="p-6 bg-white rounded-xl shadow-lg dark:bg-gray-800 transition-all duration-300 hover:shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Étape de production
            </h1>
            <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-200">
                Commande #<?= $productionStep->order->reference ?>
            </span>
        </div>
        
        <div class="mb-6 p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2"><?= ucfirst($productionStep->step) ?></h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                <?= $productionStep->comments ?? 'Aucun commentaire' ?>
            </p>
        </div>
        
        <form action="<?= PUBLIC_URL ?>production-step/update/<?= $productionStep->order_id ?>/<?= $productionStep->id ?>" method="POST" class="space-y-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut*</label>
                <select name="status" required
                        class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                    <option value="pending" <?= $productionStep->status === 'pending' ? 'selected' : '' ?>>En attente</option>
                    <option value="in_progress" <?= $productionStep->status === 'in_progress' ? 'selected' : '' ?>>En cours</option>
                    <option value="completed" <?= $productionStep->status === 'completed' ? 'selected' : '' ?>>Terminé</option>
                    <option value="on_hold" <?= $productionStep->status === 'on_hold' ? 'selected' : '' ?>>En pause</option>
                    <option value="failed" <?= $productionStep->status === 'failed' ? 'selected' : '' ?>>Problème</option>
                </select>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigné à</label>
                <input type="text" name="assigned_to" value="<?= htmlspecialchars($productionStep->assigned_to ?? '') ?>"
                       class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Commentaires</label>
                <textarea name="comments" rows="3"
                          class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300"
                          ><?= htmlspecialchars($productionStep->comments ?? '') ?></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de début</label>
                    <input type="datetime-local" name="start_time" value="<?= $productionStep->start_time ? date('Y-m-d\TH:i', strtotime($productionStep->start_time)) : '' ?>"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de fin</label>
                    <input type="datetime-local" name="end_time" value="<?= $productionStep->end_time ? date('Y-m-d\TH:i', strtotime($productionStep->end_time)) : '' ?>"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="<?= PUBLIC_URL ?>order/show/<?= $productionStep->order_id ?>" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition duration-300">
                    Retour
                </a>
                <button type="submit" class="px-6 py-2.5 text-white bg-orange-500 rounded-lg shadow-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:bg-orange-600 dark:hover:bg-orange-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293z" />
                    </svg>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>