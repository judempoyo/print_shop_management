<div class="max-w-4xl mx-auto mt-8">
    <div class="p-6 bg-white rounded-xl shadow-lg dark:bg-gray-800 transition-all duration-300 hover:shadow-xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline-block mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Nouvelle Commande
            </h1>
        </div>
        
        <form action="<?= $this->basePath ?>/order/store" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client*</label>
                    <select name="customer_id" required
                            class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                        <option value="">Sélectionner un client</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer->id ?>"><?= htmlspecialchars($customer->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de livraison*</label>
                    <input type="date" name="delivery_date" required min="<?= date('Y-m-d') ?>"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priorité*</label>
                    <select name="priority" required
                            class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                        <option value="low">Basse</option>
                        <option value="medium" selected>Moyenne</option>
                        <option value="high">Haute</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                    <select name="status"
                            class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                        <option value="received" selected>Reçue</option>
                        <option value="in_preparation">En préparation</option>
                        <option value="in_printing">En impression</option>
                        <option value="in_finishing">En finition</option>
                    </select>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" rows="3"
                          class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300"
                          placeholder="Instructions spéciales..."></textarea>
            </div>
            
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Matériaux nécessaires</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matériau</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantité</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <?php foreach ($materials as $material): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                    <?= htmlspecialchars($material->name) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                    <?= $material->stock_quantity ?> <?= $material->unit ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="number" name="materials[<?= $material->id ?>]" min="0" step="0.01"
                                           class="w-24 px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 pt-6">
                <a href="<?= $this->basePath ?>/order" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition duration-300">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 text-white bg-blue-500 rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-600 dark:hover:bg-blue-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Créer la commande
                </button>
            </div>
        </form>
    </div>
</div>