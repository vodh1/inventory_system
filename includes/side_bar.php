<div class="bg-gradient-to-b from-red-900 to-red-800 w-64 p-5 flex flex-col text-white h-screen fixed left-0 top-0 z-50 transition-transform duration-300 transform shadow-xl" id="sidebar">
    <a href="#" class="text-2xl font-bold mb-10 flex items-center">
        <i class="fas fa-boxes-stacked mr-3"></i>
        <span><?= $_SESSION['role'] == 'admin' ? 'Inventory System' : 'Borrowing System' ?></span>
    </a>
    <div class="flex-1">
        <div class="space-y-2">
            <?php if ($_SESSION['role'] == 'admin') {  ?>
                <div class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-chart-line mr-3 w-5 text-center"></i>
                    <a href="../admin/dashboard.php" class="text-white hover:text-white/90">Dashboard</a>
                </div>
                <div class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-boxes mr-3 w-5 text-center"></i>
                    <a href="../admin/inventory.php" class="text-white hover:text-white/90">Inventory</a>
                </div>
                <div class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-exchange-alt mr-3 w-5 text-center"></i>
                    <a href="../admin/transaction.php" class="text-white hover:text-white/90">Transactions</a>
                </div>
                <div class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-chart-bar mr-3 w-5 text-center"></i>
                    <a href="../admin/account.php" class="text-white hover:text-white/90">Accounts</a>
                </div>
                <div class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-file-invoice mr-3 w-5 text-center"></i>
                    <a href="../admin/request.php" class="text-white hover:text-white/90">Request</a>
                </div>
            <?php } ?>
            <?php if ($_SESSION['role'] == 'user') { ?>
                <div class="    flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-exchange-alt mr-3 w-5 text-center"></i>
                    <a href="../user/user_interface.php" class="text-white hover:text-white/90">Available Items</a>
                </div>
                <div class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-white/10 hover:translate-x-2">
                    <i class="fas fa-chart-bar mr-3 w-5 text-center"></i>
                    <a href="../user/myborrowings.php" class="text-white hover:text-white/90">My Borrowings</a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="pt-5 border-t border-white/20">
        <div class="flex flex-col items-center mb-4">
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <div class="w-16 h-16 rounded-full overflow-hidden mb-3 ring-2 ring-white/30">
                    <img src="../<?= $profile_image ?>" alt="Profile" class="w-full h-full object-cover">
                </div>
            <?php } ?>
            <span class="font-medium"><?= $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?></span>
            <small class="text-white/70"><?= ucwords($_SESSION['role']) ?></small>
            <a href="../admin/logout.php" class="mt-3 flex items-center p-2 rounded-lg transition-all duration-200 hover:bg-white/10">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>