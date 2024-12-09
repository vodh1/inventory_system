<div class="hidden lg:flex lg:w-64 lg:flex-col bg-primary p-6 gap-10 h-screen sticky top-0">
            <a href="../user/index.php" class="text-2xl font-medium text-white">Borrowing System</a>
            
            <div class="flex flex-col gap-2">
                <a href="../user/user_interface.php" class="nav-link flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all duration-300 text-white opacity-80 hover:bg-white/10 hover:opacity-100">
                    <i class='bx bx-package'></i>Available Items
                </a>
                <a href="../user/myborrowings.php" class="nav-link flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all duration-300 text-white opacity-80 hover:bg-white/10 hover:opacity-100">
                    <i class='bx bx-list-ul'></i>My Borrowings
                </a>
                <a href="../user/help.php" class="nav-link flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all duration-300 text-white opacity-80 hover:bg-white/10 hover:opacity-100">
                    <i class='bx bx-help-circle'></i>Help
                </a>
            </div>
            
            <div class="mt-auto bg-white/10 p-5 rounded-lg">
                <div class="flex flex-col items-center gap-2.5">
            <img src="/assets/default-profile.png" alt="Profile" class="w-12 h-12 rounded-full">
                    <span class="text-white"><?php echo $user_info ? $user_info['username'] : 'Guest'; ?></span>
                    <small class="text-white/80"><?php echo $user_info ? $user_info['role'] : 'Not available'; ?></small>
                </div>
            </div>
        </div>
        
<!-- Add the navigation JavaScript -->
<script src="../js/user-nav.js"></script>
