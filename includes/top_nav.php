<div class="bg-white border-b border-gray-200 p-4 sticky top-0 z-40">
    <div class="flex items-center justify-between">
        <div class="flex-1 relative flex items-center gap-2.5">
            <?php
            $url = $_SERVER['REQUEST_URI'];
            if ($url == str_contains($url, "inventory.php") || $url == str_contains($url, "account.php")) {
            ?>
                <input type="text" id="custom-search" placeholder="<?= ($url == str_contains($url, "account.php")) ? ' Search Account' : ' Search Equipment' ?>"
                    class="w-full py-2 lg:py-3 px-4 pl-10 border border-gray-200 rounded-lg text-sm">
                <i class="fas fa-search absolute left-3 text-gray-400"></i>
                <div class="ml-4">
                    <select id="category-filter" class="px-3 py-2 border border-gray-200 rounded-lg bg-white">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php } ?>
            <?php
            $url = $_SERVER['REQUEST_URI'];
            if ($url == str_contains($url, "transaction.php")) {
            ?>
                <input type="text" id="custom-search" placeholder=" Search Equipment"
                    name="search" class="w-full py-2 lg:py-3 px-4 pl-10 border border-gray-200 rounded-lg text-sm">
                <i class="fas fa-search absolute left-3 text-gray-400"></i>
            <?php } ?>
        </div>
        <!--
        <div class="hidden lg:flex items-center gap-3">
            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="w-10 h-10 rounded-full">
            <div>
                <span class="block text-gray-700">Admin User</span>
                <small class="text-sm text-gray-600">Administrator</small>
            </div>
        </div>
        -->
    </div>
</div>