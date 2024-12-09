<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve Borrow Request
    if (isset($_POST['approve_borrow_request']) && isset($_POST['request_id'])) {
        $requestId = $_POST['request_id'];
        try {
            $result = $borrowing->approveBorrowRequest($requestId);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Borrow request approved successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to approve borrow request.']);
            }
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    // Reject Borrow Request
    if (isset($_POST['reject_borrow_request']) && isset($_POST['request_id'])) {
        $requestId = $_POST['request_id'];
        try {
            $result = $borrowing->rejectBorrowRequest($requestId);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Borrow request rejected successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to reject borrow request.']);
            }
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}