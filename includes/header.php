<?php
require_once '../classes/database.class.php';
require_once '../libs/enums.php';
$profile_image = 'assets/default-profile.png'; // Default image if user not logged in

if (isset($_SESSION['user_id'])) {
    $db = new Database();
    $conn = $db->connect();

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT profile_image FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (!empty($user['profile_image'])) {
            $profile_image = $user['profile_image'];
        } else {
            $profile_image = '../assets/default-profile.png'; // Default image if user has no profile image
        }
    } else {
        $profile_image = '../assets/default-profile.png'; // Default image if user not found
    }
} else {
    $profile_image = '../assets/default-profile.png'; // Default image if user not logged in
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/snackbar.js"></script>
</head>