<?php
session_start();
require_once '../classes/database.class.php';
require_once '../libs/enums.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->connect();

    // Prepare and execute the query
    $sql = "SELECT users.id, users.username, users.password, role.name AS role, users.first_name, users.last_name, users.profile_image FROM users INNER JOIN role ON role.id = users.role_id WHERE users.username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the password
        if (md5($password) === $user['password']) { // Direct comparison for plain text passwords
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == Role::Administrator->value) {
                header('Location: ../admin/dashboard.php');
            } elseif ($user['role'] == Role::Staff->value) {
                header('Location: ../admin/transaction.php');
            } else {
                header('Location: ../user/user_interface.php');
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-cover bg-center relative" style="background-image: url('/api/placeholder/1920/1080')">
    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-purple-500/20"></div>

    <!-- Login card -->
    <div class="relative z-10 bg-white p-8 rounded-xl w-full max-w-md shadow-lg mx-4">
        <!-- Logo section -->
        <div class="text-center mb-6">
            <div class="w-24 h-24 rounded-full bg-red-600 mx-auto flex items-center justify-center text-white text-3xl mb-4">
                IS
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-red-600 text-2xl text-center font-semibold mb-8">
            INVENTORY SYSTEM
        </h1>

        <!-- Form -->
        <form method="POST" action="login.php">
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-6">
                <label for="username" class="block text-gray-700 text-sm mb-2">
                    Username
                </label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    class="w-full px-3 py-3 border border-gray-200 rounded-lg bg-gray-50 text-sm focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/10">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm mb-2">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-3 py-3 border border-gray-200 rounded-lg bg-gray-50 text-sm focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/10">
            </div>

            <button
                type="submit"
                class="w-full py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors mb-4">
                Continue
            </button>
        </form>
    </div>
</body>

</html>