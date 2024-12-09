<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';

header('Content-Type: application/json');

try {
    $equipment = new Equipment();
    $equipment_list = $equipment->showAll();

    // Prepare the response data
    $data = [];
    foreach ($equipment_list as $item) {
        $actions = '<div class="flex space-x-2">';
        $actions .= '<button class="editBtn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600" data-id="'.$item['id'].'">Edit</button>';
        $actions .= '<button class="deleteBtn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" data-id="'.$item['id'].'" data-name="'.htmlspecialchars($item['name']).'">Delete</button>';
        $actions .= '</div>';
        
        $data[] = [
            $item['name'],
            '<img src="'.htmlspecialchars($item['image_path']).'" alt="'.htmlspecialchars($item['name']).'" class="w-16 h-16 object-cover">', // Image column
            $item['description'],
            $item['category_name'],
            $item['available_units'] . ' / ' . $item['total_units'],
            $actions
        ];
    }

    $response = [
        'status' => 'success',
        'data' => $data
    ];

    echo json_encode($response);
} catch (Exception $e) {
    // Handle any errors
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    echo json_encode($response);
}
exit;
?>