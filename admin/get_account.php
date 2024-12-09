<?php
require_once '../classes/database.class.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $conn = new Database();
    $pdo = $conn->connect();

    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User ID is required']);
}
?>