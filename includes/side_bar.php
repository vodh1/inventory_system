<?php
$links = [
    ['name' => 'Dashboard', 'url' => '../admin/dashboard.php', 'icon' => 'fas fa-chart-line', 'role_access' => [Role::Administrator->value]],
    ['name' => 'Inventory', 'url' => '../admin/inventory.php', 'icon' => 'fas fa-boxes', 'role_access' => [Role::Administrator->value]],
    ['name' => 'Transactions', 'url' => '../admin/transaction.php', 'icon' => 'fas fa-exchange-alt', 'role_access' => [Role::Administrator->value, Role::Staff->value]],
    ['name' => 'Accounts', 'url' => '../admin/account.php', 'icon' => 'fas fa-chart-bar', 'role_access' => [Role::Administrator->value]],
    ['name' => 'Available Items', 'url' => '../user/user_interface.php', 'icon' => 'fas fa-exchange-alt', 'role_access' => [Role::User->value]],
    ['name' => 'My Borrowings', 'url' => '../user/myborrowings.php', 'icon' => 'fas fa-chart-bar', 'role_access' => [Role::User->value]]
];
?>
<div class="bg-gradient-to-b from-red-900 to-red-800 w-64 p-5 flex flex-col text-white h-screen fixed left-0 top-0 z-50 transition-transform duration-300 transform shadow-xl" id="sidebar">
    <a href="#" class="text-2xl font-bold mb-10 flex items-center">
        <i class="fas fa-boxes-stacked mr-3"></i>
        <span><?= $_SESSION['role'] == Role::Administrator->value ? 'Inventory System' : 'Borrowing System' ?></span>
    </a>
    <div class="flex-1">
        <div class="space-y-2">
            <?php foreach ($links as $link):
                if (in_array($_SESSION['role'], $link['role_access'])) {
            ?>
                    <div class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                        <i class="<?= $link['icon'] ?> mr-3 w-5 text-center"></i>
                        <a href="<?= $link['url'] ?>" class="text-white hover:text-white/90"><?= $link['name'] ?></a>
                    </div>
            <?php }
            endforeach ?>

        </div>
    </div>
    <div class="pt-5 border-t border-white/20">
        <div class="flex flex-col items-center mb-4">

            <div class="w-16 h-16 rounded-full overflow-hidden mb-3 ring-2 ring-white/30">
                <img src="<?= !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] :  '../assets/default-profile.png' ?>" alt="Profile" class="w-full h-full object-cover">
            </div>
            <span class="font-medium"><?= $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?></span>
            <small class="text-white/70"><?= ucwords($_SESSION['role']) ?></small>
            <a href="../admin/logout.php" class="mt-3 flex items-center p-2 rounded-lg transition-all duration-200 hover:bg-white/10">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>