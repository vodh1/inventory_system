<?php
require_once 'database.class.php';

class Transaction {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function fetchTransactions($status_filter = 'all', $start_date = null, $end_date = null, $search_query = '', $offset = 0, $limit = 10) {
        $conditions = [];
        $params = [];
        $types = '';

        if ($status_filter !== 'all') {
            if ($status_filter === 'active') {
                $conditions[] = "(eu.status = 'borrowed' OR b.status = 'approved') AND b.status != 'returned'";
            } elseif ($status_filter === 'pending') {
                $conditions[] = "b.status = 'pending'";
            } else {
                $conditions[] = "b.status = ?";
                $params[] = $status_filter;
                $types .= 's';
            }
        }

        if ($start_date && $end_date) {
            $conditions[] = "b.borrow_date BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= 'ss';
        }

        if ($search_query) {
            $conditions[] = "(e.name LIKE ? OR b.borrower_name LIKE ? OR eu.unit_code LIKE ? OR u.department LIKE ?)";
            $search_param = "%{$search_query}%";
            $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
            $types .= 'ssss';
        }

        $sql = "SELECT b.*, e.name AS equipment_name, e.image_path, eu.unit_code, eu.status as unit_status, 
                       u.department, c.name AS category_name
                FROM borrowings b 
                JOIN equipment_units eu ON b.unit_id = eu.id 
                JOIN equipment e ON eu.equipment_id = e.id 
                LEFT JOIN users u ON b.borrower_name = u.username
                LEFT JOIN categories c ON e.category_id = c.id";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY b.created_at DESC";

        // Only apply limit if it's greater than 0
        if ($limit > 0) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        try {
            $stmt = $this->db->connect()->prepare($sql);
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value);
                }
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching transactions: " . $e->getMessage());
            return [];
        }
    }

    public function fetchAllTransactions($status_filter = 'all', $start_date = null, $end_date = null, $search_query = '') {
        $conditions = [];
        $params = [];

        if ($status_filter !== 'all') {
            if ($status_filter === 'active') {
                $conditions[] = "(eu.status = 'borrowed' OR b.status = 'approved') AND b.status != 'returned'";
            } elseif ($status_filter === 'pending') {
                $conditions[] = "b.status = 'pending'";
            } else {
                $conditions[] = "b.status = ?";
                $params[] = $status_filter;
            }
        }

        if ($start_date && $end_date) {
            $conditions[] = "b.borrow_date BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
        }

        if ($search_query) {
            $conditions[] = "(e.name LIKE ? OR b.borrower_name LIKE ? OR eu.unit_code LIKE ? OR u.department LIKE ?)";
            $search_param = "%{$search_query}%";
            $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
        }

        $sql = "SELECT b.*, e.name AS equipment_name, e.image_path, eu.unit_code, eu.status as unit_status, 
                       u.department, c.name AS category_name
                FROM borrowings b 
                JOIN equipment_units eu ON b.unit_id = eu.id 
                JOIN equipment e ON eu.equipment_id = e.id 
                LEFT JOIN users u ON b.borrower_name = u.username
                LEFT JOIN categories c ON e.category_id = c.id";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY b.created_at DESC";

        try {
            $stmt = $this->db->connect()->prepare($sql);
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value);
                }
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching transactions: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalTransactions($status_filter = 'all', $start_date = null, $end_date = null, $search_query = '') {
        $conditions = [];
        $params = [];
        $types = '';

        if ($status_filter !== 'all') {
            if ($status_filter === 'active') {
                $conditions[] = "(eu.status = 'borrowed' AND b.status != 'returned')";
            } elseif ($status_filter === 'pending') {
                $conditions[] = "b.status = 'pending'";
            } else {
                $conditions[] = "b.status = ?";
                $params[] = $status_filter;
                $types .= 's';
            }
        }

        if ($start_date && $end_date) {
            $conditions[] = "b.borrow_date BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= 'ss';
        }

        if ($search_query) {
            $conditions[] = "(e.name LIKE ? OR b.borrower_name LIKE ? OR eu.unit_code LIKE ? OR u.department LIKE ?)";
            $search_param = "%{$search_query}%";
            $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
            $types .= 'ssss';
        }

        $sql = "SELECT COUNT(*) as total 
                FROM borrowings b 
                JOIN equipment_units eu ON b.unit_id = eu.id
                JOIN equipment e ON eu.equipment_id = e.id
                JOIN users u ON b.borrower_name = u.username";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->db->connect()->prepare($sql);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function markAsReturned($borrowing_id) {
        try {
            $conn = $this->db->connect();
            $conn->beginTransaction();

            // First check if the borrowing exists and is not already returned
            $check_sql = "SELECT b.status, b.unit_id 
                         FROM borrowings b
                         WHERE b.id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute([$borrowing_id]);
            $borrowing = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$borrowing) {
                throw new Exception("Borrowing record not found");
            }

            if ($borrowing['status'] === 'returned') {
                throw new Exception("This item has already been returned");
            }

            // 1. Update the borrowing record
            $update_borrowing_sql = "UPDATE borrowings 
                                   SET status = 'returned', 
                                       return_date = NOW()
                                   WHERE id = ?";
            $update_borrowing_stmt = $conn->prepare($update_borrowing_sql);
            $update_borrowing_stmt->execute([$borrowing_id]);

            // 2. Update the equipment unit status
            $update_unit_sql = "UPDATE equipment_units 
                              SET status = 'available'
                              WHERE id = ?";
            $update_unit_stmt = $conn->prepare($update_unit_sql);
            $update_unit_stmt->execute([$borrowing['unit_id']]);

            $conn->commit();
            return ['success' => true, 'message' => 'Item successfully marked as returned'];
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Database error in markAsReturned: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred. Please try again.'];
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>