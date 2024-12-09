<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';

header('Content-Type: application/json');

$conn = new Database();
$equipment = new Equipment();

$items_per_page = 3;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;
$category = isset($_GET['category']) ? (int)$_GET['category'] : '';
$search_query = isset($_GET['search']) ? $conn->connect()->quote('%' . $_GET['search'] . '%') : '';

// Debugging: Check if the connection is successful
if ($conn->connect()) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}

// Get total number of equipment
$total_sql = "SELECT COUNT(DISTINCT e.id) as total FROM equipment e 
              JOIN categories c ON e.category_id = c.id";

$where_conditions = [];

if ($category) {
    $where_conditions[] = "e.category_id = $category";
}
if (!empty($search_query)) {
    $where_conditions[] = "(e.name LIKE $search_query OR c.name LIKE $search_query)";
}

if (!empty($where_conditions)) {
    $total_sql .= " WHERE " . implode(' AND ', $where_conditions);
}

$total_result = $conn->connect()->query($total_sql);

if (!$total_result) {
    die("Database query failed: " . $conn->connect()->errorInfo()[2]);
}

$total_equipment = $total_result->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_equipment / $items_per_page);

// Get available equipment with pagination
$sql = "SELECT e.*, c.name AS category_name, 
        (SELECT COUNT(*) FROM equipment_units 
         WHERE equipment_id = e.id AND status = 'available') as available_units
        FROM equipment e
        JOIN categories c ON e.category_id = c.id";

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(' AND ', $where_conditions);
}

$sql .= " LIMIT $items_per_page OFFSET $offset";

$result = $conn->connect()->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->connect()->errorInfo()[2]);
}

$equipment_data = [];
while ($equipment = $result->fetch(PDO::FETCH_ASSOC)) {
    $equipment_data[] = $equipment;
}

echo json_encode([
    'equipment' => $equipment_data,
    'total_equipment' => $total_equipment,
    'total_pages' => $total_pages
]);
?>