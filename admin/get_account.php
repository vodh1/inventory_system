<?php
require_once '../classes/database.class.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $conn = new Database();
    $pdo = $conn->connect();

    $sql = "SELECT users.*, role.id AS role, role.name AS role_name, department.id AS department, department.name AS department_name FROM users INNER JOIN department ON department.id = users.department_id INNER JOIN role ON role.id = users.role_id WHERE users.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User ID is required']);
}
