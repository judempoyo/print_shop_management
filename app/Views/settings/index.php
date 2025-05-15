<!-- views/settings/index.php -->
<div class="max-w-5xl mx-auto p-6">
    <!-- En-tête -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Paramètres de l'application
        </h1>
    </div>

    <form action="<?= PUBLIC_URL ?>settings" method="post" class="space-y-8">
        <!-- Section Tarifs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gradient-to-r from-teal-50 to-purple-50 dark:from-gray-700 dark:to-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                    <i class="fas fa-tags mr-2 text-teal-500"></i>
                    Gestion des tarifs
                </h2>
                <button type="button" id="add-price-btn" class="inline-flex items-center px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600 transition-colors duration-300">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Ajouter un tarif
                </button>
            </div>
            
            <div class="p-6">
              <div id="price-entries" class="space-y-4">
    <?php 
    $index = 0;
    foreach($settings['session_prices'] as $type => $price): ?>
        <div class="price-entry flex items-center space-x-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type de séance</label>
                    <input type="text" name="session_prices[<?= $index ?>][type]" 
                           value="<?= htmlspecialchars($type) ?>"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-800 dark:text-white transition-all duration-300"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (€)</label>
                    <input type="number" name="session_prices[<?= $index ?>][price]" 
                           value="<?= htmlspecialchars($price) ?>"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-800 dark:text-white transition-all duration-300"
                           step="0.01" min="0" required>
                </div>
            </div>
            <button type="button" class="remove-price-btn px-3 py-2 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        <?php $index++; ?>
    <?php endforeach; ?>
</div>
            </div>
        </div>
        
        <!-- Section Apparence -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-teal-50 to-purple-50 dark:from-gray-700 dark:to-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                    <i class="fas fa-palette mr-2 text-teal-500"></i>
                    Personnalisation de l'apparence
                </h2>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Couleur principale
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="color" name="theme_color" 
                                   value="<?= htmlspecialchars($settings['theme_color']) ?>"
                                   class="w-16 h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer">
                        </div>
                        <div class="flex-1">
                            <input type="text" value="<?= htmlspecialchars($settings['theme_color']) ?>" 
                                   class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg font-mono text-sm" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Prévisualisation
                    </label>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium" 
                              style="background-color: <?= $settings['theme_color'] ?>20; color: <?= $settings['theme_color'] ?>">
                            Exemple
                        </span>
                        <span class="px-3 py-1 rounded-lg text-sm font-medium text-white" 
                              style="background-color: <?= $settings['theme_color'] ?>">
                            Bouton
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Boutons de soumission -->
        <div class="flex justify-end space-x-4">
            <a href="<?= $this->basePath ?>" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                Annuler
            </a>
            <button type="submit" class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-300 flex items-center">
                <i class="fas fa-save mr-2"></i>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ajout d'un nouveau tarif
    document.getElementById('add-price-btn').addEventListener('click', function() {
        const container = document.getElementById('price-entries');
        const index = document.querySelectorAll('.price-entry').length;
        
        const html = `
        <div class="price-entry flex items-center space-x-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type de séance</label>
                    <input type="text" name="session_prices[][type]" 
                           placeholder="Ex: Portrait"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-800 dark:text-white transition-all duration-300"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (€)</label>
                    <input type="number" name="session_prices[][price]" 
                           placeholder="Ex: 150"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-800 dark:text-white transition-all duration-300"
                           step="0.01" min="0" required>
                </div>
            </div>
            <button type="button" class="remove-price-btn px-3 py-2 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>`;
        
        container.insertAdjacentHTML('beforeend', html);
        
        // Animation d'apparition
        const entries = container.querySelectorAll('.price-entry');
        const newEntry = entries[entries.length - 1];
        newEntry.style.opacity = '0';
        newEntry.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            newEntry.style.transition = 'all 0.3s ease';
            newEntry.style.opacity = '1';
            newEntry.style.transform = 'translateY(0)';
        }, 10);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-price-btn')) {
            const entry = e.target.closest('.price-entry');
            
            // Animation de disparition
            entry.style.transition = 'all 0.3s ease';
            entry.style.opacity = '0';
            entry.style.height = '0';
            entry.style.padding = '0';
            entry.style.margin = '0';
            entry.style.border = '0';
            
            // Suppression après l'animation
            setTimeout(() => {
                entry.remove();
                // Réindexation complète après suppression
                reindexPriceEntries();
            }, 300);
        }
    });

    // Fonction pour réindexer proprement tous les champs
    function reindexPriceEntries() {
        const entries = document.querySelectorAll('.price-entry');
        entries.forEach((entry, index) => {
            // Sélectionne tous les inputs dans l'entrée
            const inputs = entry.querySelectorAll('input[type="text"], input[type="number"]');
            
            // Réattribue les noms avec le nouvel index
            inputs[0].name = `session_prices[${index}][type]`;
            inputs[1].name = `session_prices[${index}][price]`;
        });
    }

    // Mise à jour de la prévisualisation de la couleur
    const colorPicker = document.querySelector('input[name="theme_color"]');
    const colorPreview = document.querySelector('input[readonly]');
    
    colorPicker.addEventListener('input', function() {
        colorPreview.value = this.value;
        document.querySelectorAll('[style*="background-color"]').forEach(el => {
            el.style.backgroundColor = this.value + (el.classList.contains('bg-opacity-20') ? '20' : '');
            if (!el.classList.contains('text-white')) {
                el.style.color = this.value;
            }
        });
    });
});
</script>