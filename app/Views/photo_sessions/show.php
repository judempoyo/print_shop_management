<div class="max-w-4xl p-6 mx-auto mt-8 bg-white rounded-lg shadow dark:bg-gray-800">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Détails de la Séance</h1>
        <a href="<?= PUBLIC_URL ?>photo-session" class="text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300">
            ← Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-gray-200">Informations</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Type</p>
                    <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($session->type) ?></p>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Description</p>
                    <p class="text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($session->notes)) ?></p>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Date</p>
                    <p class="text-gray-900 dark:text-white"><?= date('d/m/Y H:i', strtotime($session->date)) ?></p>
                </div>
                
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Statut</p>
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                        <?= match($session->status) {
                            'planned' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'completed' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'processed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                        } ?>">
                        <?= ucfirst($session->status) ?>
                    </span>
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-gray-200">Client</h2>
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($session->customer->name) ?></p>
                <p class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($session->customer->phone) ?></p>
                <?php if (!empty($session->customer->email)): ?>
                    <p class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($session->customer->email) ?></p>
                <?php endif; ?>
            </div>

            <div class="mt-6">
                 <form action="<?= $baseUrl ?>/photo-session/<?= $session->id ?>/status" method="post" class="inline-flex items-center space-x-2 mb-4">
        <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <?php foreach (App\Models\PhotoSession::getStatuses() as $value => $label): ?>
                <option value="<?= $value ?>" <?= $session->status === $value ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
            Mettre à jour
        </button>
    </form>
                <a href="<?= PUBLIC_URL ?>photo-session/edit/<?= $session->id ?>" 
                class="inline-flex items-center px-4 py-2 ml-2 text-white bg-teal-500 rounded hover:bg-teal-600 dark:bg-teal-600 dark:hover:bg-teal-700">
                Modifier
            </a>
            <form action="<?= $baseUrl ?>/photo-session/<?= $session->id ?>/delete"
                              method="post" class="inline-flex items-center">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 text-white  text-white bg-red-500 rounded hover:bg-red-600"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette seance ?')">
                               Supprimer
                            </button>
                        </form>
           
            </div>
        </div>
    </div>
     <div class="mt-8">
        <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-gray-200">Photos</h2>
        
        <!-- Formulaire d'upload -->
        <div class="p-4 mb-6 bg-gray-50 rounded-lg dark:bg-gray-700">
            <form action="<?= $baseUrl ?>/photo-session/<?= $session->id ?>/photos" 
                  method="post" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Ajouter des photos
                    </label>
                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" 
                           type="file" name="photos[]" multiple accept="image/*">
                </div>
                <button type="submit" class="px-4 py-2 text-white bg-teal-500 rounded hover:bg-teal-600 dark:bg-teal-600 dark:hover:bg-teal-700">
                    Uploader
                </button>
            </form>
        </div>

        <!-- Galerie de photos -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
             <?php foreach ($photos as $photo): ?>
        <div class="relative group">
            <img src="<?= $baseUrl ?>/<?= $photo->path ?>" 
                 class="object-cover w-full h-40 rounded-lg">
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 rounded-lg transition-opacity">
                        <form action="<?= $baseUrl ?>/photo-session/<?= $session->id ?>/photos/delete/<?= $photo->id ?>" 
                              method="post">
                            <button type="submit" 
                                    class="p-2 text-white bg-red-500 rounded-full hover:bg-red-600"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette photo ?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>