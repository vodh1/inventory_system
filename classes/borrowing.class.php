<?php
require_once 'database.class.php';

class Borrowing {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function fetchBorrowRequests() {
        $sql = "SELECT b.*, e.name AS equipment_name, eu.unit_code, u.department, c.name AS category_name 
                FROM borrowings b 
                JOIN equipment_units eu ON b.unit_id = eu.id 
                JOIN equipment e ON eu.equipment_id = e.id 
                JOIN users u ON b.borrower_name = u.username 
                JOIN categories c ON e.category_id = c.id 
                ORDER BY b.created_at DESC";
    
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function submitBorrowRequest($equipment_id, $borrower_name, $borrow_date, $return_date, $purpose, $unit_code) {
        try {
            $pdo = $this->db->connect();

            // Start transaction
            $pdo->beginTransaction();

            // Fetch the unit_id based on the unit_code
            $unit_sql = "SELECT id FROM equipment_units WHERE unit_code = :unit_code";
            $unit_stmt = $pdo->prepare($unit_sql);
            $unit_stmt->bindParam(':unit_code', $unit_code);
            $unit_stmt->execute();
            $unit_result = $unit_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$unit_result) {
                throw new Exception("Unit not found for unit code: " . $unit_code);
            }

            $unit_id = $unit_result['id'];

            // Fetch the equipment name based on the equipment_id
            $equipment_sql = "SELECT name FROM equipment WHERE id = :equipment_id";
            $equipment_stmt = $pdo->prepare($equipment_sql);
            $equipment_stmt->bindParam(':equipment_id', $equipment_id);
            $equipment_stmt->execute();
            $equipment_result = $equipment_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$equipment_result) {
                throw new Exception("Equipment not found for equipment ID: " . $equipment_id);
            }

            $equipment_name = $equipment_result['name'];

            // Insert borrowing record with status 'pending'
            $sql = "INSERT INTO borrowings (unit_id, borrower_name, borrow_date, return_date, purpose, status, approval_status, created_at, equipment_name, unit_code) 
                    VALUES (:unit_id, :borrower_name, :borrow_date, :return_date, :purpose, 'pending', 'pending', NOW(), :equipment_name, :unit_code)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':unit_id', $unit_id);
            $stmt->bindParam(':borrower_name', $borrower_name);
            $stmt->bindParam(':borrow_date', $borrow_date);
            $stmt->bindParam(':return_date', $return_date);
            $stmt->bindParam(':purpose', $purpose);
            $stmt->bindParam(':equipment_name', $equipment_name);
            $stmt->bindParam(':unit_code', $unit_code);
            
            if (!$stmt->execute()) {
                throw new Exception("Error inserting borrowing record: " . $stmt->errorInfo()[2]);
            }

            // Decrement the available units count
            $update_available_units_sql = "UPDATE equipment SET available_units = available_units - 1 WHERE id = :equipment_id";
            $update_available_units_stmt = $pdo->prepare($update_available_units_sql);
            $update_available_units_stmt->bindParam(':equipment_id', $equipment_id);
            if (!$update_available_units_stmt->execute()) {
                throw new Exception("Error executing update available units query: " . $update_available_units_stmt->errorInfo()[2]);
            }

            // Commit transaction
            $pdo->commit();
            return ['status' => 'success', 'message' => 'Borrow request submitted successfully'];
        } catch (Exception $e) {
            // Rollback transaction on error
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function approveBorrowRequest($request_id) {
        try {
            $pdo = $this->db->connect();

            // Start transaction
            $pdo->beginTransaction();

            // Fetch the unit_id associated with the request
            $fetch_unit_sql = "SELECT unit_id FROM borrowings WHERE id = :request_id";
            $fetch_unit_stmt = $pdo->prepare($fetch_unit_sql);
            $fetch_unit_stmt->bindParam(':request_id', $request_id);
            $fetch_unit_stmt->execute();
            $fetch_unit_result = $fetch_unit_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fetch_unit_result) {
                throw new Exception("Request not found for request ID: " . $request_id);
            }

            $unit_id = $fetch_unit_result['unit_id'];

            // Update the status of the borrowing record to 'approved'
            $update_borrowing_sql = "UPDATE borrowings SET approval_status = 'approved', status = 'active' WHERE id = :request_id";
            $update_borrowing_stmt = $pdo->prepare($update_borrowing_sql);
            $update_borrowing_stmt->bindParam(':request_id', $request_id);
            if (!$update_borrowing_stmt->execute()) {
                throw new Exception("Error executing update borrowing status query: " . $update_borrowing_stmt->errorInfo()[2]);
            }

            // Update equipment unit status to 'borrowed'
            $update_sql = "UPDATE equipment_units SET status = 'borrowed' WHERE id = :unit_id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->bindParam(':unit_id', $unit_id);
            if (!$update_stmt->execute()) {
                throw new Exception("Error updating equipment unit status: " . $update_stmt->errorInfo()[2]);
            }

            // Add a notification
            $notification_sql = "INSERT INTO notifications (message, created_at) VALUES (:message, NOW())";
            $notification_stmt = $pdo->prepare($notification_sql);
            $message = "Borrow request #" . $request_id . " has been approved.";
            $notification_stmt->bindParam(':message', $message);
            if (!$notification_stmt->execute()) {
                throw new Exception("Error inserting notification: " . $notification_stmt->errorInfo()[2]);
            }

            // Commit transaction
            $pdo->commit();
            return ['status' => 'success', 'message' => 'Borrow request approved successfully'];
        } catch (Exception $e) {
            // Rollback transaction on error
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function rejectBorrowRequest($request_id) {
        try {
            $pdo = $this->db->connect();

            // Start transaction
            $pdo->beginTransaction();

            // Fetch the unit_id associated with the request
            $fetch_unit_sql = "SELECT unit_id FROM borrowings WHERE id = :request_id";
            $fetch_unit_stmt = $pdo->prepare($fetch_unit_sql);
            $fetch_unit_stmt->bindParam(':request_id', $request_id);
            $fetch_unit_stmt->execute();
            $fetch_unit_result = $fetch_unit_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fetch_unit_result) {
                throw new Exception("Request not found for request ID: " . $request_id);
            }

            $unit_id = $fetch_unit_result['unit_id'];

            // Update the status of the borrowing record to 'rejected'
            $update_borrowing_sql = "UPDATE borrowings SET approval_status = 'rejected', status = 'rejected' WHERE id = :request_id";
            $update_borrowing_stmt = $pdo->prepare($update_borrowing_sql);
            $update_borrowing_stmt->bindParam(':request_id', $request_id);
            if (!$update_borrowing_stmt->execute()) {
                throw new Exception("Error executing update borrowing status query: " . $update_borrowing_stmt->errorInfo()[2]);
            }

            // Update equipment unit status back to 'available'
            $update_sql = "UPDATE equipment_units SET status = 'available' WHERE id = :unit_id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->bindParam(':unit_id', $unit_id);
            if (!$update_stmt->execute()) {
                throw new Exception("Error updating equipment unit status: " . $update_stmt->errorInfo()[2]);
            }

            // Add a notification
            $notification_sql = "INSERT INTO notifications (message, created_at) VALUES (:message, NOW())";
            $notification_stmt = $pdo->prepare($notification_sql);
            $message = "Borrow request #" . $request_id . " has been rejected.";
            $notification_stmt->bindParam(':message', $message);
            if (!$notification_stmt->execute()) {
                throw new Exception("Error inserting notification: " . $notification_stmt->errorInfo()[2]);
            }

            // Commit transaction
            $pdo->commit();
            return ['status' => 'success', 'message' => 'Borrow request rejected successfully'];
        } catch (Exception $e) {
            // Rollback transaction on error
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getTransactionById($id) {
        try {
            $sql = "SELECT b.*, e.name AS equipment_name, eu.unit_code, u.department, 
                          u.email AS borrower_email, u.contact_number AS borrower_contact,
                          c.name AS category_name, e.description AS equipment_description,
                          e.image_path
                   FROM borrowings b 
                   JOIN equipment_units eu ON b.unit_id = eu.id 
                   JOIN equipment e ON eu.equipment_id = e.id 
                   JOIN users u ON b.borrower_name = u.username 
                   JOIN categories c ON e.category_id = c.id 
                   WHERE b.id = :id";
            
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($transaction) {
                // Format dates
                $transaction['borrow_date'] = date('M d, Y', strtotime($transaction['borrow_date']));
                $transaction['return_date'] = date('M d, Y', strtotime($transaction['return_date']));
                $transaction['created_at'] = date('M d, Y h:i A', strtotime($transaction['created_at']));
            }
            
            return $transaction;
        } catch (Exception $e) {
            return null;
        }
    }
}

?>