<?php
require_once 'session_check.php';
require_once '../classes/account.class.php';

$account = new Account();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_account'])) {
        try {
            $account->first_name = $_POST['first_name'];
            $account->middle_name = $_POST['middle_name'];
            $account->last_name = $_POST['last_name'];
            $account->age = $_POST['age'];
            $account->address = $_POST['address'];
            $account->email = $_POST['email'];
            $account->role = $_POST['role'];
            $account->department = $_POST['department'];
            $account->password = $_POST['password'];
            $account->contact_number = $_POST['contact_number'];
            $account->username = $_POST['username'];
            if ($account->add()) {
                $_SESSION['success'] = "Account added successfully";
            } else {
                $_SESSION['error'] = "Failed to add account";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    } elseif (isset($_POST['update_account'])) {
        try {
            $account->id = $_POST['user_id'];
            $account->first_name = $_POST['first_name'];
            $account->middle_name = $_POST['middle_name'];
            $account->last_name = $_POST['last_name'];
            $account->age = $_POST['age'];
            $account->address = $_POST['address'];
            $account->email = $_POST['email'];
            $account->role = $_POST['role'];
            $account->department = $_POST['department'];
            $account->password = $_POST['password'];
            $account->contact_number = $_POST['contact_number'];
            $account->username = $_POST['username'];
            $account->profile_image = $_POST['current_profile_image'];
            if ($account->update()) {
                $_SESSION['success'] = "Account updated successfully";
            } else {
                $_SESSION['error'] = "Failed to update account";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    } elseif (isset($_POST['delete_account'])) {
        try {
            $user_id = $_POST['user_id'];
            if ($account->delete($user_id)) {
                $_SESSION['success'] = "Account deleted successfully";
            } else {
                $_SESSION['error'] = "Failed to delete account";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    header('Location: account.php');
    exit();
}

$accounts = $account->fetchAccounts();
$roles = $account->fetchRoles();
$departments = $account->fetchDepartments();
?>
<?php require_once '../includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">

<body class="bg-gray-100 min-h-screen flex relative overflow-x-hidden">
    <!-- Sidebar -->
    <?php require_once '../includes/side_bar.php'; ?>
    <!-- Main Container -->
    <div class="ml-[16rem] flex-1 w-[calc(100%-16rem)]">
        <!-- Navigation -->
        <?php require_once '../includes/top_nav.php'; ?>
        <!-- Main Content -->
        <main class="p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8 flex-wrap gap-5">
                <div>
                    <h2 class="text-3xl font-bold mb-1">Accounts</h2>
                    <p class="text-gray-600">Manage system users and their access levels</p>
                </div>
                <div class="flex gap-3">
                    <button class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800" onclick="showAddAccountModal()">
                        Add New Account
                    </button>
                </div>
            </div>

            <!-- Account List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table id="accountTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($accounts as $account): ?>
                                    <tr class="transition-colors duration-200 account-row" data-department="<?php echo $account['department']; ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                                        src="<?php echo $account['profile_image'] ? $account['profile_image'] : '../assets/default-profile.png'; ?>"
                                                        alt="Profile">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-black dark:text-white account-name">
                                                <?php echo $account['first_name'] . ' ' . $account['middle_name'] . ' ' . $account['last_name']; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600 text-black dark:text-gray-300 account-email">
                                                <?php echo $account['email']; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php echo $account['role'] === Role::Administrator->value ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                                                <?php echo $account['role']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600 text-black dark:text-gray-300 account-department">
                                                <?php echo $account['department']; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                onclick="showUserDetailsModal(<?php echo $account['id']; ?>)">
                                                <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View details
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <button class="inline-flex items-center px-3 py-1.5 border border-blue-600 rounded-md text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                onclick="showEditAccountModal(<?php echo $account['id']; ?>)">
                                                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>
                                            <form class="inline-block" action="account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?')">
                                                <input type="hidden" name="user_id" value="<?php echo $account['id']; ?>">
                                                <button type="submit" name="delete_account"
                                                    class="inline-flex items-center px-3 py-1.5 border border-red-600 rounded-md text-sm font-medium text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Account Modal -->
    <div id="addAccountModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden overflow-y-auto">
        <div class="bg-white rounded-lg w-full max-w-2xl mx-4 my-6 shadow-xl max-h-[90vh] flex flex-col">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Add New Account</h2>
                <button onclick="closeAddAccountModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <form action="account.php" method="POST" enctype="multipart/form-data" id="addAccountForm" onsubmit="return validateAddAccountForm()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Personal Information</h3>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">First Name *</label>
                            <input type="text" name="first_name" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter first name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Middle Name</label>
                            <input type="text" name="middle_name" class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter middle name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Last Name *</label>
                            <input type="text" name="last_name" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter last name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Age *</label>
                            <input type="number" name="age" required min="18" max="100" class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter age">
                        </div>

                        <!-- Contact Information Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Contact Information</h3>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Email Address *</label>
                            <input type="email" name="email" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter email address" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <p class="text-sm text-gray-500">Enter a valid email address</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Contact Number *</label>
                            <input type="tel" name="contact_number" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter contact number" pattern="[0-9]{11}">
                            <p class="text-sm text-gray-500">Enter 11-digit phone number</p>
                        </div>
                        <div class="col-span-2">
                            <label class="font-medium text-gray-800">Address *</label>
                            <textarea name="address" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" rows="2" placeholder="Enter complete address"></textarea>
                        </div>

                        <!-- Account Information Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Account Information</h3>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Username *</label>
                            <input type="text" name="username" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter username" minlength="5">
                            <p class="text-sm text-gray-500">Minimum 5 characters</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Role *</label>
                            <select name="role" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Department *</label>
                            <select name="department" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= $department['id'] ?>"><?= $department['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Password *</label>
                            <input type="password" name="password" id="password" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter password" minlength="8">
                            <div id="password-strength" class="w-full h-2 rounded-full bg-gray-200 mt-1">
                                <div id="strength-bar" class="h-full rounded-full transition-all duration-300"></div>
                            </div>
                            <p class="text-sm text-gray-500">Must be at least 8 characters with numbers and special characters</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Confirm Password *</label>
                            <input type="password" id="confirm_password" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Confirm password">
                            <p id="password-match" class="text-sm"></p>
                        </div>

                        <!-- Profile Image Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Profile Image</h3>
                        </div>
                        <div class="col-span-2">
                            <div class="flex items-center gap-4">
                                <div class="w-24 h-24 border-2 border-gray-300 rounded-full overflow-hidden">
                                    <img id="profile-preview" src="../assets/img/default-avatar.png" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="profile_image" accept="image/*" class="hidden" id="profile_image" onchange="previewImage(this)">
                                    <label for="profile_image" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-200">
                                        Choose Image
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Recommended: Square image, max 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2.5 mt-8">
                        <button type="button" onclick="closeAddAccountModal()" class="px-5 py-2.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit" name="add_account" class="px-5 py-2.5 rounded-lg bg-red-700 hover:bg-red-800 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div id="editAccountModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden overflow-y-auto">
        <div class="bg-white rounded-lg w-full max-w-2xl mx-4 my-6 shadow-xl max-h-[90vh] flex flex-col">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
                <h2 class="text-2xl font-bold text-gray-800">Edit Account</h2>
                <button onclick="closeEditAccountModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <form action="account.php" method="POST" enctype="multipart/form-data" id="editAccountForm" onsubmit="return validateEditAccountForm()">
                    <input type="hidden" id="editUserId" name="user_id">
                    <input type="hidden" id="current_profile_image" name="current_profile_image">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Personal Information</h3>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">First Name *</label>
                            <input type="text" name="first_name" id="edit_first_name" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter first name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Middle Name</label>
                            <input type="text" name="middle_name" id="edit_middle_name" class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter middle name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Last Name *</label>
                            <input type="text" name="last_name" id="edit_last_name" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter last name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Age *</label>
                            <input type="number" name="age" id="edit_age" required min="18" max="100" class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter age">
                        </div>

                        <!-- Contact Information Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Contact Information</h3>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Email Address *</label>
                            <input type="email" name="email" id="edit_email" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter email address" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <p class="text-sm text-gray-500">Enter a valid email address</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Contact Number *</label>
                            <input type="tel" name="contact_number" id="edit_contact_number" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter contact number" pattern="[0-9]{11}">
                            <p class="text-sm text-gray-500">Enter 11-digit phone number</p>
                        </div>
                        <div class="col-span-2">
                            <label class="font-medium text-gray-800">Address *</label>
                            <textarea name="address" id="edit_address" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" rows="2" placeholder="Enter complete address"></textarea>
                        </div>

                        <!-- Account Information Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Account Information</h3>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Username *</label>
                            <input type="text" name="username" id="edit_username" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter username" minlength="5">
                            <p class="text-sm text-gray-500">Minimum 5 characters</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Role *</label>
                            <select name="role" id="edit_role" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">Department *</label>
                            <select name="department" id="edit_department" required class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= $department['id'] ?>"><?= $department['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-medium text-gray-800">New Password</label>
                            <input type="password" name="password" id="edit_password" class="p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter new password" minlength="8">
                            <div id="edit-password-strength" class="w-full h-2 rounded-full bg-gray-200 mt-1">
                                <div id="edit-strength-bar" class="h-full rounded-full transition-all duration-300"></div>
                            </div>
                            <p class="text-sm text-gray-500">Leave blank to keep current password</p>
                        </div>

                        <!-- Profile Image Section -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Profile Image</h3>
                        </div>
                        <div class="col-span-2">
                            <div class="flex items-center gap-4">
                                <div class="w-24 h-24 border-2 border-gray-300 rounded-full overflow-hidden">
                                    <img id="edit-profile-preview" src="../assets/img/default-avatar.png" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="profile_image" accept="image/*" class="hidden" id="edit_profile_image" onchange="previewEditImage(this)">
                                    <label for="edit_profile_image" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-200">
                                        Choose New Image
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Recommended: Square image, max 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2.5 mt-8">
                        <button type="button" onclick="closeEditAccountModal()" class="px-5 py-2.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit" name="update_account" class="px-5 py-2.5 rounded-lg bg-red-700 hover:bg-red-800 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Details Modal -->
    <div id="userDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-[1000] opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-lg w-full max-w-2xl mx-4 my-6 shadow-xl transform -translate-y-5 transition-transform duration-300 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="p-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
                <h2 class="text-2xl font-bold text-gray-800">Account Details</h2>
                <button onclick="closeUserDetailsModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <!-- User Profile Section -->
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Profile Image Section -->
                    <div class="md:w-1/3 flex flex-col items-center">
                        <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-gray-200 mb-4">
                            <img id="detailsProfileImage" src="../assets/img/default-avatar.png" alt="Profile Image" class="w-full h-full object-cover">
                        </div>
                        <div class="text-center">
                            <h3 id="detailsFullName" class="text-xl font-semibold text-gray-800 mb-1"></h3>
                            <span id="detailsRole" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="md:w-2/3">
                        <!-- Personal Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-200">
                                Personal Information
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">First Name</label>
                                    <p id="detailsFirstName" class="text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Middle Name</label>
                                    <p id="detailsMiddleName" class="text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Name</label>
                                    <p id="detailsLastName" class="text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Age</label>
                                    <p id="detailsAge" class="text-gray-800"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-200">
                                Contact Information
                            </h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                                    <p id="detailsEmail" class="text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Contact Number</label>
                                    <p id="detailsContactNumber" class="text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                                    <p id="detailsAddress" class="text-gray-800"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-200">
                                Account Information
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Username</label>
                                    <p id="detailsUsername" class="text-gray-800"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Department</label>
                                    <p id="detailsDepartment" class="text-gray-800"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#accountTable').DataTable({
                paging: true,
                searching: true,
                dom: 'lrtip',
                ordering: true,
                info: true,
                lengthChange: false,
                pageLength: 5,
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Search functionality
            $('#custom-search').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Category filter functionality
            $('#category-filter').on('change', function() {
                var category = $(this).val();
                if (category === '') {
                    table.column(4).search('').draw(); // Reset search
                } else {
                    table.column(4).search(category, true, false).draw();
                }
            });

            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#strength-bar');
                let strength = 0;

                // Length check
                if (password.length >= 8) strength += 25;

                // Contains number
                if (/\d/.test(password)) strength += 25;

                // Contains lowercase
                if (/[a-z]/.test(password)) strength += 25;

                // Contains uppercase or special char
                if (/[A-Z]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength += 25;

                // Update strength bar
                strengthBar.css('width', strength + '%');

                // Update color based on strength
                if (strength < 50) {
                    strengthBar.css('background-color', '#ef4444');
                } else if (strength < 75) {
                    strengthBar.css('background-color', '#eab308');
                } else {
                    strengthBar.css('background-color', '#22c55e');
                }
            });

            // Password confirmation checker
            $('#confirm_password').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();
                const matchText = $('#password-match');

                if (confirmPassword === '') {
                    matchText.text('');
                } else if (password === confirmPassword) {
                    matchText.text('Passwords match').removeClass('text-red-500').addClass('text-green-500');
                } else {
                    matchText.text('Passwords do not match').removeClass('text-green-500').addClass('text-red-500');
                }
            });
        });

        function validateAddAccountForm() {
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }

            // Password strength validation
            if (password.length < 8) {
                alert('Password must be at least 8 characters long!');
                return false;
            }

            if (!/\d/.test(password)) {
                alert('Password must contain at least one number!');
                return false;
            }

            if (!/[a-z]/.test(password)) {
                alert('Password must contain at least one lowercase letter!');
                return false;
            }

            if (!/[A-Z]/.test(password) && !/[^A-Za-z0-9]/.test(password)) {
                alert('Password must contain at least one uppercase letter or special character!');
                return false;
            }

            return true;
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }

        function showAddAccountModal() {
            document.getElementById('addAccountModal').classList.remove('hidden');
        }

        function closeAddAccountModal() {
            document.getElementById('addAccountModal').classList.add('hidden');
        }

        function showEditAccountModal(userId) {
            fetch('get_account.php?id=' + userId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editUserId').value = data.id;
                    document.getElementById('edit_first_name').value = data.first_name;
                    document.getElementById('edit_middle_name').value = data.middle_name;
                    document.getElementById('edit_last_name').value = data.last_name;
                    document.getElementById('edit_age').value = data.age;
                    document.getElementById('edit_address').value = data.address;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_role').value = data.role;
                    document.getElementById('edit_department').value = data.department;
                    document.getElementById('edit_password').value = data.password;
                    document.getElementById('edit_contact_number').value = data.contact_number;
                    document.getElementById('edit_username').value = data.username;
                    document.getElementById('current_profile_image').value = data.profile_image;
                });
            document.getElementById('editAccountModal').classList.remove('hidden');
        }

        function closeEditAccountModal() {
            document.getElementById('editAccountModal').classList.add('hidden');
        }

        function showUserDetailsModal(userId) {
            const modal = document.getElementById('userDetailsModal');
            modal.style.display = 'flex';

            fetch(`get_account.php?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    document.getElementById('detailsProfileImage').src = data.profile_image || '../assets/default-profile.png';
                    document.getElementById('detailsFullName').textContent = `${data.first_name} ${data.middle_name} ${data.last_name}`;
                    document.getElementById('detailsRole').textContent = data.role_name;

                    // Fix role badge classes
                    const roleBadge = document.getElementById('detailsRole');
                    if (data.role === 'admin') {
                        roleBadge.classList.add('bg-purple-100');
                        roleBadge.classList.add('text-purple-800');
                    } else {
                        roleBadge.classList.add('bg-green-100');
                        roleBadge.classList.add('text-green-800');
                    }

                    document.getElementById('detailsFirstName').textContent = data.first_name;
                    document.getElementById('detailsMiddleName').textContent = data.middle_name;
                    document.getElementById('detailsLastName').textContent = data.last_name;
                    document.getElementById('detailsAge').textContent = data.age;
                    document.getElementById('detailsEmail').textContent = data.email;
                    document.getElementById('detailsContactNumber').textContent = data.contact_number;
                    document.getElementById('detailsAddress').textContent = data.address;
                    document.getElementById('detailsUsername').textContent = data.username;
                    document.getElementById('detailsDepartment').textContent = data.department_name;

                    setTimeout(() => {
                        modal.classList.add('opacity-100');
                        modal.querySelector('.transform').classList.remove('-translate-y-5');
                    }, 10);
                })
                .catch(error => {
                    alert('Error loading user details: ' + error.message);
                    closeUserDetailsModal();
                });
        }

        function closeUserDetailsModal() {
            const modal = document.getElementById('userDetailsModal');
            modal.classList.remove('opacity-100');
            modal.querySelector('.transform').classList.add('-translate-y-5');
            setTimeout(() => {
                modal.style.display = 'none';
                modal.classList.add('hidden');
                modal.querySelector('.transform').classList.remove('-translate-y-5');

                // Reset the form fields
                document.getElementById('detailsProfileImage').src = '../assets/img/default-avatar.png';
                document.getElementById('detailsFullName').textContent = '';
                document.getElementById('detailsRole').textContent = '';
                const roleBadge = document.getElementById('detailsRole');
                roleBadge.classList.remove('bg-purple-100', 'text-purple-800', 'bg-green-100', 'text-green-800');

                // Reset all detail fields
                ['FirstName', 'MiddleName', 'LastName', 'Age', 'Email',
                    'ContactNumber', 'Address', 'Username', 'Department'
                ].forEach(field => {
                    document.getElementById('details' + field).textContent = '';
                });
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('addAccountModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddAccountModal();
            }
        });

        document.getElementById('editAccountModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditAccountModal();
            }
        });

        document.getElementById('userDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUserDetailsModal();
            }
        });

        function validateEditAccountForm() {
            const password = $('#edit_password').val();

            // Only validate password if it's being changed
            if (password) {
                if (password.length < 8) {
                    alert('New password must be at least 8 characters long!');
                    return false;
                }

                if (!/\d/.test(password)) {
                    alert('New password must contain at least one number!');
                    return false;
                }

                if (!/[a-z]/.test(password)) {
                    alert('New password must contain at least one lowercase letter!');
                    return false;
                }

                if (!/[A-Z]/.test(password) && !/[^A-Za-z0-9]/.test(password)) {
                    alert('New password must contain at least one uppercase letter or special character!');
                    return false;
                }
            }

            return true;
        }

        function previewEditImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#edit-profile-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }

        // Password strength checker for edit form
        $('#edit_password').on('input', function() {
            const password = $(this).val();
            const strengthBar = $('#edit-strength-bar');
            let strength = 0;

            if (!password) {
                strengthBar.css('width', '0%');
                return;
            }

            // Length check
            if (password.length >= 8) strength += 25;

            // Contains number
            if (/\d/.test(password)) strength += 25;

            // Contains lowercase
            if (/[a-z]/.test(password)) strength += 25;

            // Contains uppercase or special char
            if (/[A-Z]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength += 25;

            // Update strength bar
            strengthBar.css('width', strength + '%');

            // Update color based on strength
            if (strength < 50) {
                strengthBar.css('background-color', '#ef4444');
            } else if (strength < 75) {
                strengthBar.css('background-color', '#eab308');
            } else {
                strengthBar.css('background-color', '#22c55e');
            }
        });
    </script>
</body>

</html>

<?php require_once '../includes/footer.php'; ?>