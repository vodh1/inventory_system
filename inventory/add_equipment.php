<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $equipment = new Equipment();
    $equipment->name = $_POST['name'];
    $equipment->description = $_POST['description'];
    $equipment->category_id = (int)$_POST['category_id'];
    $equipment->max_borrow_days = (int)$_POST['max_borrow_days'];
    $equipment->units = isset($_POST['units']) ? (int)$_POST['units'] : 1;

    $errors = [];

    // Validate name
    if (empty($equipment->name)) {
        $errors['nameErr'] = "Name is required";
    }

    // Validate description
    if (empty($equipment->description)) {
        $errors['descriptionErr'] = "Description is required";
    }

    // Validate category
    if (empty($equipment->category_id)) {
        $errors['categoryErr'] = "Category is required";
    }

    // Validate max_borrow_days
    if (empty($equipment->max_borrow_days)) {
        $errors['max_borrow_daysErr'] = "Maximum Borrow Days is required";
    }

    // Validate units
    if (empty($equipment->units)) {
        $errors['unitsErr'] = "Number of Units is required";
    }

    // Validate image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $equipment->image_path = upload_image($_FILES['image']);
        if (!$equipment->image_path) {
            $errors['imageErr'] = "Failed to upload image";
        }
    }

    if (empty($errors)) {
        if ($equipment->add()) {
            // Fetch the last added equipment with its details
            $last_added = $equipment->fetchLastAdded();
            echo json_encode([
                'status' => 'success', 
                'message' => 'Equipment added successfully',
                'equipment' => $last_added
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add equipment']);
        }
    } else {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
    }
}

function upload_image($file) {
    $target_dir = "../uploads/equipment/"; // Ensure the path is correct
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $allowed_types = ['jpg', 'jpeg', 'png'];
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    return false;
}
?>