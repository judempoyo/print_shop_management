<div class="max-w-4xl mx-auto mt-8">
    <div class="p-6 bg-white rounded-xl shadow-lg dark:bg-gray-800 transition-all duration-300 hover:shadow-xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline-block mr-2 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <h2>Modifier Commande #<?= $order->id ?></h2>
            </h1>
        </div>
        
        <form action="<?= PUBLIC_URL ?>order/update/<?= $order->id ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client*</label>
                    <select name="customer_id" required
                            class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer->id ?>" <?= $customer->id === $order->customer_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($customer->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de livraison*</label>
                    <input type="date" name="delivery_date" value="<?= $order->delivery_date ?>" required
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priorité*</label>
                    <select name="priority" required
                            class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                        <option value="low" <?= $order->priority === 'low' ? 'selected' : '' ?>>Basse</option>
                        <option value="medium" <?= $order->priority === 'medium' ? 'selected' : '' ?>>Moyenne</option>
                        <option value="high" <?= $order->priority === 'high' ? 'selected' : '' ?>>Haute</option>
                        <option value="urgent" <?= $order->priority === 'urgent' ? 'selected' : '' ?>>Urgente</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut*</label>
                    <select name="status" required
                            class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300">
                        <option value="received" <?= $order->status === 'received' ? 'selected' : '' ?>>Reçue</option>
                        <option value="in_preparation" <?= $order->status === 'in_preparation' ? 'selected' : '' ?>>En préparation</option>
                        <option value="in_printing" <?= $order->status === 'in_printing' ? 'selected' : '' ?>>En impression</option>
                        <option value="in_finishing" <?= $order->status === 'in_finishing' ? 'selected' : '' ?>>En finition</option>
                        <option value="ready_for_delivery" <?= $order->status === 'ready_for_delivery' ? 'selected' : '' ?>>Prête à livrer</option>
                        <option value="delivered" <?= $order->status === 'delivered' ? 'selected' : '' ?>>Livrée</option>
                        <option value="canceled" <?= $order->status === 'canceled' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" rows="3"
                          class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300"
                          ><?= htmlspecialchars($order->notes) ?></textarea>
            </div>
            
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Matériaux utilisés</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matériau</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantité utilisée</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <?php foreach ($materialsList as $material):
                                $used = $order->materials->firstWhere('id', $material->id)?->pivot?->quantity_used ?? 0;
                            ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                    <?= htmlspecialchars($material->name) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                    <?= $material->stock_quantity ?> <?= $material->unit ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="number" name="materials[<?= $material->id ?>]" 
                                           value="<?= $used ?>" min="0" step="0.01"
                                           class="w-24 px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 pt-6">
                <a href="<?= PUBLIC_URL ?>order" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition duration-300">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 text-white bg-teal-500 rounded-lg shadow-sm hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:bg-teal-600 dark:hover:bg-teal-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293z" />
                    </svg>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>