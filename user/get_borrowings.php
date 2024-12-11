<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/borrowing.class.php';

$conn = new Database();
$borrowing = new Borrowing();

// Fetch user information (assuming user ID is stored in session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Pagination Logic
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Fetch all borrowings for the user
$sql = "SELECT b.*, e.name AS equipment_name, e.image_path, eu.unit_code 
        FROM borrowings b 
        JOIN equipment_units eu ON b.unit_id = eu.id 
        JOIN equipment e ON eu.equipment_id = e.id 
        WHERE b.user_id = (SELECT id FROM users WHERE id = :user_id) 
        ORDER BY b.status, b.borrow_date DESC 
        LIMIT :offset, :items_per_page";

$stmt = $conn->connect()->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of borrowings
$total_borrowings_sql = "SELECT COUNT(*) as total 
                         FROM borrowings b 
                         JOIN equipment_units eu ON b.unit_id = eu.id 
                         JOIN equipment e ON eu.equipment_id = e.id 
                         WHERE b.user_id = (SELECT id FROM users WHERE id = :user_id)";

$total_stmt = $conn->connect()->prepare($total_borrowings_sql);
$total_stmt->bindParam(':user_id', $user_id);
$total_stmt->execute();
$total_borrowings = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Calculate counts for active, pending, and completed borrowings
$active_count_sql = "SELECT COUNT(*) as total FROM borrowings WHERE status = 'active' AND user_id = (SELECT id FROM users WHERE id = :user_id)";
$active_stmt = $conn->connect()->prepare($active_count_sql);
$active_stmt->bindParam(':user_id', $user_id);
$active_stmt->execute();
$active_count = $active_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$pending_count_sql = "SELECT COUNT(*) as total FROM borrowings WHERE status = 'pending' AND user_id = (SELECT id FROM users WHERE id = :user_id)";
$pending_stmt = $conn->connect()->prepare($pending_count_sql);
$pending_stmt->bindParam(':user_id', $user_id);
$pending_stmt->execute();
$pending_count = $pending_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$completed_count_sql = "SELECT COUNT(*) as total FROM borrowings WHERE status = 'returned' AND user_id = (SELECT id FROM users WHERE id = :user_id)";
$completed_stmt = $conn->connect()->prepare($completed_count_sql);
$completed_stmt->bindParam(':user_id', $user_id);
$completed_stmt->execute();
$completed_count = $completed_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Prepare the response
$response = [
    'borrowings' => $borrowings,
    'total_borrowings' => $total_borrowings,
    'current_page' => $current_page,
    'items_per_page' => $items_per_page,
    'active_count' => $active_count,
    'pending_count' => $pending_count,
    'completed_count' => $completed_count
];

echo json_encode($response);
