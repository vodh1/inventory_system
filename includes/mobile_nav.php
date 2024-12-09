<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="flex justify-around p-4">
            <a href="index.php" class="flex flex-col items-center text-gray-500">
                <i class='bx bx-package text-2xl'></i>
                <span class="text-xs">Items</span>
            </a>
            <a href="my_borrowings.php" class="flex flex-col items-center text-primary">
                <i class='bx bx-list-ul text-2xl'></i>
                <span class="text-xs">Borrowings</span>
            </a>
            <a href="help.php" class="flex flex-col items-center text-gray-500">
                <i class='bx bx-help-circle text-2xl'></i>
                <span class="text-xs">Help</span>
            </a>
            <button id="profileToggle" class="flex flex-col items-center text-gray-500">
                <i class='bx bx-user text-2xl'></i>
                <span class="text-xs">Profile</span>
            </button>
        </div>
    </div>

    <!-- Mobile Profile Drawer -->
    <div id="profileDrawer" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="absolute right-0 top-0 bottom-0 w-64 bg-white p-6 transform translate-x-full transition-transform duration-300">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold">Profile</h3>
                <button id="closeProfile" class="text-2xl">&times;</button>
            </div>
            <div class="flex flex-col items-center gap-4">
                <img src="assets/profile-1.png" alt="Profile" class="w-20 h-20 rounded-full">
                <div class="text-center">
                    <p class="font-medium"><?php echo $user_info ? $user_info['username'] : 'Guest'; ?></p>
                    <p class="text-sm text-gray-500"><?php echo $user_info ? $user_info['department'] : 'Not available'; ?></p>
                </div>
            </div>
        </div>
    </div>