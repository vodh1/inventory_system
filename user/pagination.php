<?php
require_once '../classes/database.class.php';
require_once '../libs/enums.php';

function getPaginatedEquipment($items_per_page, $current_page, $category = '', $search_query = '')
{
    $conn = new Database();
    $pdo = $conn->connect();

    // Calculate offset
    $offset = ($current_page - 1) * $items_per_page;

    // Get total number of equipment
    $total_sql = "SELECT COUNT(DISTINCT e.id) as total FROM equipment e 
                  JOIN categories c ON e.category_id = c.id";

    $where_conditions = [];

    if ($category) {
        $where_conditions[] = "e.category_id = :category";
    }
    if (!empty($search_query)) {
        $where_conditions[] = "(e.name LIKE :search_query OR c.name LIKE :search_query)";
    }

    if (!empty($where_conditions)) {
        $total_sql .= " WHERE " . implode(' AND ', $where_conditions);
    }

    $total_stmt = $pdo->prepare($total_sql);
    if ($category) $total_stmt->bindParam(':category', $category);
    if (!empty($search_query)) $total_stmt->bindValue(':search_query', '%' . $search_query . '%');
    $total_stmt->execute();
    $total_equipment = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get equipment with pagination
    $sql = "SELECT e.*, c.name AS category_name, 
            (SELECT COUNT(*) FROM equipment_units 
             WHERE equipment_id = e.id AND status = :status) as available_units
            FROM equipment e
            JOIN categories c ON e.category_id = c.id";

    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(' AND ', $where_conditions);
    }

    $sql .= " LIMIT :limit OFFSET :offset";
    $status = UnitStatus::Available->value;
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $status);
    if ($category) $stmt->bindParam(':category', $category);
    if (!empty($search_query)) $stmt->bindValue(':search_query', '%' . $search_query . '%');
    $stmt->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $equipment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate HTML content
    $html = '';
    foreach ($equipment_data as $equipment) {
        $html .= generateEquipmentRowHTML($equipment);
    }

    // Generate pagination links
    $total_pages = ceil($total_equipment / $items_per_page);
    $pagination_html = '<div class="flex flex-col lg:flex-row justify-between items-center gap-4 p-4 lg:p-5 bg-white rounded-lg shadow-sm">';
    $pagination_html .= '<div class="text-sm text-gray-500 text-center lg:text-left">';
    $pagination_html .= 'Showing ' . ($offset + 1) . ' to ' . min($offset + $items_per_page, $total_equipment) . ' of ' . $total_equipment . ' entries';
    $pagination_html .= '</div>';
    $pagination_html .= '<div class="flex flex-wrap justify-center gap-1">';
    for ($i = 1; $i <= $total_pages; $i++) {
        $pagination_html .= '<a href="#" onclick="fetchEquipment(' . $i . '); return false;" class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-primary hover:text-white transition-colors ' . ($i == $current_page ? 'bg-primary text-white' : 'bg-white text-gray-700') . '">' . $i . '</a>';
    }
    $pagination_html .= '</div>';
    $pagination_html .= '</div>';

    return [
        'html' => $html,
        'pagination' => $pagination_html
    ];
}

function generateEquipmentRowHTML($equipment)
{
    $html = '<div class="bg-white rounded-xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">';
    $html .= '<div class="relative h-40 lg:h-48 overflow-hidden">';
    $html .= '<img src="' . (!empty($equipment['image_path']) ? $equipment['image_path'] : '../uploads/equipment/default_image_equipment.png') . '" alt="' . $equipment['name'] . '" class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-110">';
    $html .= '<div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>';
    $html .= '</div>';
    $html .= '<div class="p-4 transition-colors duration-300 group-hover:bg-gray-50">';
    $html .= '<h3 class="text-lg font-medium text-gray-800 mb-2 transition-colors duration-300 group-hover:text-primary">' . $equipment['name'] . '</h3>';
    $html .= '<p class="text-sm text-gray-500 mb-4">Category: ' . $equipment['category_name'] . '</p>';
    $html .= '<div class="space-y-1.5 mb-5">';
    $html .= '<p class="flex items-center gap-2 text-sm text-gray-600 transition-transform duration-300 group-hover:translate-x-1">';
    $html .= '<i class="bx bx-check-circle text-green-600"></i>';
    $html .= $equipment['available_units'] . ' units available';
    $html .= '</p>';
    $html .= '<p class="flex items-center gap-2 text-sm text-gray-600 transition-transform duration-300 group-hover:translate-x-1">';
    $html .= '<i class="bx bx-time-five text-primary"></i>';
    $html .= 'Max ' . $equipment['max_borrow_days'] . ' days borrowing';
    $html .= '</p>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="p-4 flex gap-3 border-t border-gray-100 transition-colors duration-300 group-hover:bg-gray-50">';
    $html .= $equipment['available_units'] > 0 ? '<button onclick="showBorrowModal(' . $equipment['id'] . ')" class="flex-1 py-2.5 lg:py-3 bg-primary text-white rounded-lg transition-all duration-300 hover:bg-primary-hover hover:shadow-md">Borrow equipment</button>' : '<button class="flex-1 py-2.5 lg:py-3 bg-gray-300 text-gray-700 rounded-lg cursor-not-allowed" disabled>No units available</button>';
    $html .= '<button onclick="showDetailsModal(' . $equipment['id'] . ')" class="flex-1 py-2.5 lg:py-3 bg-gray-50 text-gray-700 rounded-lg border border-gray-200 transition-all duration-300 hover:bg-gray-100 hover:shadow-md">View details</button>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'paginate') {
    $items_per_page = isset($_GET['items_per_page']) ? (int)$_GET['items_per_page'] : 3;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $category = isset($_GET['category']) ? (int)$_GET['category'] : '';
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

    $data = getPaginatedEquipment($items_per_page, $current_page, $category, $search_query);
    echo json_encode($data);
    exit;
}
