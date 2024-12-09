<?php
require_once '../classes/database.class.php';

header('Content-Type: application/json');

function sendErrorResponse($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Input validation
if (!isset($_GET['id'])) {
    sendErrorResponse('Equipment ID is required', 400);
}

$equipment_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($equipment_id === false) {
    sendErrorResponse('Invalid equipment ID format', 400);
}

try {
    $db = new Database();
    $conn = $db->connect();

    // Get equipment details with error checking
    $stmt = $conn->prepare("
        SELECT id, name, description, category_id, max_borrow_days, image_path 
        FROM equipment 
        WHERE id = :equipment_id
    ");
    
    if (!$stmt) {
        throw new Exception('Failed to prepare equipment query: ' . $conn->errorInfo()[2]);
    }
    
    $stmt->bindParam(':equipment_id', $equipment_id, PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute equipment query: ' . $stmt->errorInfo()[2]);
    }
    
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipment) {
        sendErrorResponse('Equipment not found', 404);
    }

    // Get unit details with error checking
    $stmt = $conn->prepare("
        SELECT eu.unit_code, eu.status,
               b.borrower_name, b.borrow_date, 
               DATE_ADD(b.borrow_date, INTERVAL :max_borrow_days DAY) as expected_return,
               b.status as borrow_status,
               (SELECT COUNT(*) FROM borrowings WHERE unit_id = eu.id AND status = 'pending') as pending_count
        FROM equipment_units eu
        LEFT JOIN borrowings b ON eu.id = b.unit_id AND b.status IN ('active', 'pending')
        WHERE eu.equipment_id = :equipment_id
    ");
    
    if (!$stmt) {
        throw new Exception('Failed to prepare units query: ' . $conn->errorInfo()[2]);
    }
    
    $stmt->bindParam(':max_borrow_days', $equipment['max_borrow_days'], PDO::PARAM_INT);
    $stmt->bindParam(':equipment_id', $equipment_id, PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute units query: ' . $stmt->errorInfo()[2]);
    }
    
    $units_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $units = [];
    $counts = ['available' => 0, 'borrowed' => 0, 'maintenance' => 0, 'pending' => 0];
    
    foreach ($units_result as $unit) {
        $status = $unit['status'];
        if ($unit['borrow_status'] == 'pending') {
            $status = 'pending';
        } elseif ($unit['borrow_status'] == 'active') {
            $status = 'borrowed';
        }
        
        $counts[$status]++;
        
        $units[] = [
            'unit_code' => $unit['unit_code'],
            'status' => $status,
            'borrower' => $unit['borrower_name'],
            'date_borrowed' => $unit['borrow_date'] ? 
                date('M d, Y', strtotime($unit['borrow_date'])) : null,
            'expected_return' => $unit['expected_return'] ? 
                date('M d, Y', strtotime($unit['expected_return'])) : null,
            'pending_count' => $unit['pending_count']
        ];
    }

    // Debugging: Print the fetched units data
    error_log(print_r($units, true));

    // Ensure the response is valid JSON
    $response = json_encode([
        'equipment' => $equipment,
        'units' => $units,
        'counts' => $counts
    ]);
    

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON encoding error: ' . json_last_error_msg());
    }

    echo $response;

} catch (Exception $e) {
    error_log('Equipment details error: ' . $e->getMessage());
    sendErrorResponse('Error loading equipment details: ' . $e->getMessage());
}
?>