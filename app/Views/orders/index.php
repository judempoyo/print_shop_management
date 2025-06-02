  <?php if ($_SESSION['flash']): ?>
            <div class="<?= $_SESSION['flash']['type'] == 'success'? 'bg-green-100 border-l-4 border-green-500 p-4 rounded mb-4 dark:bg-green-900/30 dark:border-green-400' :'bg-red-100 border-l-4 border-red-500 p-4 rounded mb-4 dark:bg-red-900/30 dark:border-red-400'?>">
                <p class="text-red-700 dark:text-red-200"><?= htmlspecialchars($_SESSION['flash']['message']) ?></p>
            </div>
        <?php endif; ?>
<?= $datatable ?>