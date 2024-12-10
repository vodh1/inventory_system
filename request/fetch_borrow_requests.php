<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';

$conn = new Database();
$equipment = new Equipment();

// Read parameters
$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];
$category_id = $_POST['category_id'];
$search_value = $_POST['search_value'];

// Build the SQL query
$sql = "SELECT b.*, e.name AS equipment_name, eu.unit_code, u.department, c.name AS category_name 
        FROM borrowings b 
        JOIN equipment_units eu ON b.unit_id = eu.id 
        JOIN equipment e ON eu.equipment_id = e.id 
        JOIN users u ON b.borrower_username = u.username 
        JOIN categories c ON e.category_id = c.id";

$where = [];
if (!empty($search_value)) {
    $where[] = "b.borrower_username LIKE :search OR e.name LIKE :search OR eu.unit_code LIKE :search OR u.department LIKE :search OR c.name LIKE :search";
}
if (!empty($category_id)) {
    $where[] = "e.category_id = :category_id";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY b.created_at DESC LIMIT :start, :length";

$stmt = $conn->connect()->prepare($sql);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':length', $length, PDO::PARAM_INT);
if (!empty($search_value)) {
    $search = "%$search_value%";
    $stmt->bindParam(':search', $search);
}
if (!empty($category_id)) {
    $stmt->bindParam(':category_id', $category_id);
}

$stmt->execute();
$borrow_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total records
$total_records_sql = "SELECT COUNT(*) as total FROM borrowings";
$total_records_result = $conn->connect()->query($total_records_sql);
$total_records = $total_records_result->fetch(PDO::FETCH_ASSOC)['total'];

// Get filtered records
$filtered_records_sql = "SELECT COUNT(*) as total FROM borrowings b 
                         JOIN equipment_units eu ON b.unit_id = eu.id 
                         JOIN equipment e ON eu.equipment_id = e.id 
                         JOIN users u ON b.borrower_username = u.username 
                         JOIN categories c ON e.category_id = c.id";

if (!empty($where)) {
    $filtered_records_sql .= " WHERE " . implode(" AND ", $where);
}

$filtered_records_stmt = $conn->connect()->prepare($filtered_records_sql);
if (!empty($search_value)) {
    $filtered_records_stmt->bindParam(':search', $search);
}
if (!empty($category_id)) {
    $filtered_records_stmt->bindParam(':category_id', $category_id);
}

$filtered_records_stmt->execute();
$filtered_records = $filtered_records_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Prepare response
$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($total_records),
    "recordsFiltered" => intval($filtered_records),
    "data" => []
];

foreach ($borrow_requests as $row) {
    $response['data'][] = [
        "id" => $row['id'],
        "equipment_name" => $row['equipment_name'],
        "unit_code" => $row['unit_code'],
        "borrower_username" => $row['borrower_username'],
        "department" => $row['department'],
        "borrow_date" => $row['borrow_date'],
        "return_date" => $row['return_date'],
        "purpose" => $row['purpose'],
        "approval_status" => $row['approval_status'],
        "category_name" => $row['category_name'],
        "action" => ($row['approval_status'] == 'pending') ?
            '<button class="px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm hover:opacity-90 transition-opacity duration-300 approve-btn" data-request-id="' . $row['id'] . '">Approve</button>' .
            '<button class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm hover:opacity-90 transition-opacity duration-300 reject-btn" data-request-id="' . $row['id'] . '">Reject</button>' : ''
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
