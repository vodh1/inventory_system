<?php
$total_items = $conn->query("SELECT COUNT(*) as total FROM equipment")->fetch(PDO::FETCH_ASSOC)['total'];
$items_borrowed = $conn->query("SELECT COUNT(*) as total FROM borrowings WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC)['total'];
$overdue_items = $conn->query("SELECT COUNT(*) as total FROM borrowings WHERE status = 'active' AND return_date < NOW()")->fetch(PDO::FETCH_ASSOC)['total'];
$active_users = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM borrowings WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch data for charts
$borrowing_trends = $conn->query("SELECT DATE(borrow_date) as date, COUNT(*) as count FROM borrowings GROUP BY DATE(borrow_date) ORDER BY date DESC LIMIT 7")->fetchAll(PDO::FETCH_ASSOC);
$equipment_categories = $conn->query("SELECT c.name, COUNT(e.id) as count FROM equipment e JOIN categories c ON e.category_id = c.id GROUP BY c.name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent activities
$recent_activities = $conn->query("
    (SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) AS borrower_name, e.name AS equipment_name, d.name AS department, 'borrowed' AS activity_type 
     FROM borrowings b 
     JOIN equipment_units eu ON b.unit_id = eu.id 
     JOIN equipment e ON eu.equipment_id = e.id 
     JOIN users u ON b.user_id = u.id
     INNER JOIN department d ON u.department_id = d.id
     WHERE b.status = 'active' 
     ORDER BY b.borrow_date DESC LIMIT 5)
    UNION
    (SELECT b.*,CONCAT(u.first_name, ' ', u.last_name) AS borrower_name, e.name AS equipment_name, d.name AS department, 'returned' AS activity_type 
     FROM borrowings b 
     JOIN equipment_units eu ON b.unit_id = eu.id 
     JOIN equipment e ON eu.equipment_id = e.id 
     JOIN users u ON b.user_id = u.id 
     INNER JOIN department d ON u.department_id = d.id
     WHERE b.status = 'returned' 
     ORDER BY b.return_date DESC LIMIT 5)
    ORDER BY borrow_date DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
