<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';
require_once '../classes/borrowing.class.php'; // New class for borrowing operations

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $equipment_id = intval($_POST['equipment_id']);
    $borrow_date = $_POST['borrow_date'];
    $return_date = $_POST['return_date'];
    $purpose = $_POST['purpose'];
    $unit_code = $_POST['unit']; // Get the selected unit number

    // Use the logged-in user's name (assuming it's stored in the session)
    if (isset($_SESSION['username'])) {
        $borrower_name = $_SESSION['username'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Borrower name not found in session.']);
        exit();
    }

    try {
        $borrowing = new Borrowing();
        $result = $borrowing->submitBorrowRequest(
            $equipment_id,
            $borrower_name,
            $borrow_date,
            $return_date,
            $purpose,
            $unit_code
        );

        if ($result['status'] === 'success') {
            echo json_encode(['status' => 'success', 'message' => 'Borrow request submitted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        }
        exit();

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>