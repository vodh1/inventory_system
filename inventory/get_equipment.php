<?php
require_once '../classes/database.class.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $equipment_id = intval($_GET['id']);

    $conn = new Database();
    $pdo = $conn->connect();

    try {
        
        $sql = "SELECT e.*, c.name AS category_name, 
                (SELECT COUNT(*) FROM equipment_units WHERE equipment_id = e.id AND status = 'available') as available_units,
                (SELECT GROUP_CONCAT(unit_code) FROM equipment_units WHERE equipment_id = e.id AND status = 'available') as units
                FROM equipment e
                JOIN categories c ON e.category_id = c.id
                WHERE e.id = :equipment_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':equipment_id', $equipment_id);
        $stmt->execute();

        $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($equipment) {

            $equipment['units'] = explode(',', $equipment['units']);

            echo json_encode($equipment);
        } else {
            echo json_encode(['error' => 'Equipment not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>