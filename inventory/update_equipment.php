<?php
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Unknown error occurred'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $equipment = new Equipment();
        $equipment->id = intval($_POST['equipment_id']);

        $errors = [];

        if (empty($_POST['name'])) {
            $errors['nameErr'] = 'Name is required';
        }

        if (empty($_POST['description'])) {
            $errors['descriptionErr'] = 'Description is required';
        }

        if (empty($_POST['category_id'])) {
            $errors['categoryErr'] = 'Category is required';
        }

        if (empty($_POST['max_borrow_days']) || intval($_POST['max_borrow_days']) <= 0) {
            $errors['max_borrow_daysErr'] = 'Invalid max borrow days';
        }

        if (empty($_POST['units']) || intval($_POST['units']) <= 0) {
            $errors['unitsErr'] = 'Invalid number of units';
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $equipment->image_path = upload_image($_FILES['image']);
            if (!$equipment->image_path) {
                $errors['imageErr'] = 'Failed to upload image';
            }
        }


        if (empty($errors)) {
            $equipment->name = $_POST['name'];
            $equipment->description = $_POST['description'];
            $equipment->category_id = intval($_POST['category_id']);
            $equipment->max_borrow_days = intval($_POST['max_borrow_days']);
            $equipment->units = intval($_POST['units']);

            if ($equipment->edit()) {
                // Fetch updated equipment details
                $updated_equipment = $equipment->fetchRecord($equipment->id);


                $response = [
                    'status' => 'success',
                    'message' => 'Equipment updated successfully',
                    'equipment' => $updated_equipment,
                    'total_units' => $updated_equipment['total_units']
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to update equipment'
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'errors' => $errors
            ];
        }
    } catch (Exception $e) {
        // Debug: Catch and log exceptions
        error_log("Exception: " . $e->getMessage());
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

echo json_encode($response);

function upload_image($file)
{
    $target_dir = "../uploads/equipment/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    return false;
}
