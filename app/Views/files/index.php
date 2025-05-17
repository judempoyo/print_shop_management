<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Fichiers - Commande #<?= $order->reference ?></h2>
        <a href="<?= PUBLIC_URL ?>file/upload/<?= $order->id ?>"
           class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 dark:bg-teal-600 dark:hover:bg-teal-700">
            Ajouter un fichier
        </a>
    </div>
    
    <?= $datatable ?>
</div>