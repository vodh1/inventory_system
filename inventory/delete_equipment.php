<?php
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['equipment_id'])) {
    $equipment = new Equipment();
    $equipment_id = intval($_POST['equipment_id']);

    try {
        if ($equipment->delete($equipment_id)) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Equipment deleted successfully',
                'equipment_id' => $equipment_id
            ]);
        } else {
            // Return a generic error message without debug information
            echo json_encode([
                'status' => 'error', 
                'message' => 'Failed to delete equipment'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Exception occurred: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method or missing equipment_id'
    ]);
}
?>