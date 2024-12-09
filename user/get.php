<?php
require_once '../classes/database.class.php';

header('Content-Type: application/json');

try {
    $conn = new Database();
    $pdo = $conn->connect();

    // Check if the request is to mark notifications as read
    if (isset($_GET['mark_as_read']) && $_GET['mark_as_read'] == 1) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    // Fetch notifications
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch total number of unread notifications
    $total_notifications_sql = "SELECT COUNT(*) as total FROM notifications WHERE is_read = 0";
    $stmt = $pdo->prepare($total_notifications_sql);
    $stmt->execute();
    $total_notifications = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Ensure the response is valid JSON
    $response = json_encode([
        'notifications' => $notifications,
        'total_notifications' => $total_notifications
    ]);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON encoding error: ' . json_last_error_msg());
    }

    echo $response;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>