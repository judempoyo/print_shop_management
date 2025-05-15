<div class="max-w-2xl mx-auto mt-8">
    <div class="p-6 bg-white rounded-xl shadow-lg dark:bg-gray-800 transition-all duration-300 hover:shadow-xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 inline-block mr-2 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau Client
            </h1>
        </div>
        
        <form action="<?= PUBLIC_URL ?>customer/store" method="POST" class="space-y-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom Complet*</label>
                <div class="relative">
                    <input type="text" name="name" required
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300"
                           placeholder="Jean Dupont">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <div class="relative">
                    <input type="email" name="email"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300"
                           placeholder="client@exemple.com">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone*</label>
                <div class="relative">
                    <input type="tel" name="phone" required
                           pattern="^\+?[0-9]{10,15}$"
                           title="Format: +1234567890 ou 0123456789 (10-15 chiffres)"
                           class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300"
                           placeholder="+1234567890">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: +1234567890 ou 0123456789 (10-15 chiffres)</p>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse</label>
                <textarea name="address" rows="3"
                          class="block w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition duration-300"
                          placeholder="123 Rue de l'Imprimerie, 75000 Paris"></textarea>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Préférences</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="preferences[newsletter]" value="1" 
                               class="rounded border-gray-300 text-teal-500 shadow-sm focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Recevoir la newsletter</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="preferences[sms_notifications]" value="1"
                               class="rounded border-gray-300 text-teal-500 shadow-sm focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Notifications SMS</span>
                    </label>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="<?= PUBLIC_URL ?>customer" class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition duration-300">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 text-white bg-teal-500 rounded-lg shadow-sm hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:bg-teal-600 dark:hover:bg-teal-700 transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>