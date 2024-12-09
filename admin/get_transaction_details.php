<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/borrowing.class.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No transaction ID provided']);
    exit;
}

try {
    $borrowing = new Borrowing();
    $transaction = $borrowing->getTransactionById($_GET['id']);
    
    if ($transaction) {
        echo json_encode([
            'status' => 'success',
            'data' => $transaction
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Transaction not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error fetching transaction details: ' . $e->getMessage()
    ]);
}
