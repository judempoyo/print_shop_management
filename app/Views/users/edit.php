<div class="max-w-2xl mx-auto mt-8">
    <div class="p-6 bg-white rounded-xl shadow-lg dark:bg-gray-800 transition-all duration-300 hover:shadow-xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline-block mr-2 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier Utilisateur
            </h1>
            <span class="px-3 py-1 text-xs font-semibold text-teal-800 bg-teal-100 rounded-full dark:bg-teal-900 dark:text-teal-200">
                ID: <?= $user->id ?>
            </span>
        </div>
        
        <form action="<?= PUBLIC_URL ?>user/update/<?= $user->id ?>" method="POST" class="space-y-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom Complet*</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user->name) ?>" required
                       class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300">
                <?php if (isset($sessionManager->get('errors')['name'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $sessionManager->get('errors')['name'] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email*</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user->email) ?>" required
                       class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300">
                <?php if (isset($sessionManager->get('errors')['email'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $sessionManager->get('errors')['email'] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                <div class="relative">
                    <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($user->phone) ?>" 
                           pattern="^\+?[0-9]{10,15}$"
                           title="Format: +243975889135 ou 0975889135 (10-15 chiffres)"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="button" onclick="document.getElementById('phone').value=''" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: +243975889135 ou 0975889135 (10-15 chiffres)</p>
                <?php if (isset($sessionManager->get('errors')['phone'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $sessionManager->get('errors')['phone'] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rôle*</label>
                <select name="role" required
                        class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300">
                    <option value="user" <?= $user->role === 'user' ? 'selected' : '' ?>>Utilisateur standard</option>
                    <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
                <?php if (isset($sessionManager->get('errors')['role'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $sessionManager->get('errors')['role'] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" name="password"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300"
                           placeholder="Laissez vide pour ne pas changer">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <?php if (isset($sessionManager->get('errors')['password'])): ?>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= $sessionManager->get('errors')['password'] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmer le nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" name="password_confirmation"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300"
                           placeholder="Laissez vide pour ne pas changer">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="<?= PUBLIC_URL ?>user" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition duration-300">
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