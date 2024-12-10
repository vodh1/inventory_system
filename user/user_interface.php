<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';
require_once '../user/borrow_modal.php';
require_once '../user/details_modal.php';
require_once '../libs/enums.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit();
}

$conn = new Database();
$equipment = new Equipment();

// Fetch user information (assuming user ID is stored in session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_info = null;

if ($user_id) {
    $sql = "SELECT users.username, department.name AS department, role.name AS role, users.profile_image FROM users INNER JOIN department ON department.id = users.department_id INNER JOIN role ON role.id = users.role_id WHERE users.id = :user_id";
    $stmt = $conn->connect()->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all categories for the dropdown
$categories = $equipment->fetchCategory();

// Pagination Logic
$items_per_page = 3;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get total number of equipment
$total_equipment = count($equipment->showAll());

// Get available equipment with pagination
$equipment_list = $equipment->showAll('', '', $offset, $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Borrowing System</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#b90303',
                        'primary-hover': '#a00202',
                    }
                }
            }
        }
    </script>
    <style>
        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 16px;
            background-color: #ffffff;
            color: #b90303;
            border: 1px solid #b90303;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        .pagination button:hover {
            background-color: #b90303;
            color: #ffffff;
            border-color: #a00202;
        }

        .pagination button.active {
            background-color: #b90303;
            color: #ffffff;
            border-color: #a00202;
        }

        .pagination button:disabled {
            background-color: #cccccc;
            color: #ffffff;
            border-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Mobile Navigation -->
    <?php require_once '../includes/mobile_nav.php' ?>

    <div class="flex min-h-screen">
        <!-- Sidebar - Hidden on mobile -->
        <?php require_once '../includes/side_bar.php' ?>

        <!-- Main Content -->
        <div class="flex-1 ml-[16.2rem] p-4 lg:p-5 w-full lg:max-w-[calc(100%-16rem)]">
            <!-- Navigation -->
            <div class="flex items-center gap-5 p-4 lg:p-5 bg-white rounded-lg shadow-sm sticky top-0 z-40 mb-8">
                <div class="flex-1 relative flex items-center gap-2.5">
                    <i class='bx bx-search absolute left-3 text-gray-400'></i>
                    <input type="text" id="searchInput" placeholder="Search equipment..."
                        class="w-full py-2 lg:py-3 px-4 pl-10 border border-gray-200 rounded-lg text-sm">
                    <button id="searchButton" class="ml-2 px-4 py-2 bg-primary text-white rounded-lg">Search</button>
                    <select id="categoryFilter" class="flex-1 lg:flex-none px-5 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!--<!-- Notification Bell with Dropdown -->
                <!--<div class="hidden lg:block relative">-->
                <!--    <button id="notificationBtn" class="p-2 hover:bg-gray-100 rounded-full relative">-->
                <!--        <i class='bx bx-bell text-2xl text-gray-600'></i>-->
                <!--        <span id="notificationCount" class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden"></span>-->
                <!--    </button>-->
                <!---->
                <!--    <!-- Notification Dropdown -->
                <!--    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">-->
                <!--        <!-- Header -->
                <!--        <div class="p-4 border-b border-gray-100">-->
                <!--            <div class="flex items-center justify-between">-->
                <!--                <h3 class="text-lg font-medium text-gray-800">Notifications</h3>-->
                <!--                <button class="text-gray-400 hover:text-gray-600">-->
                <!--                    <i class='bx bx-x text-xl'></i>-->
                <!--                </button>-->
                <!--            </div>-->
                <!--        </div>-->
                <!---->
                <!--        <!-- Notification List -->
                <!--        <div class="max-h-[400px] overflow-y-auto" id="notificationList">-->
                <!--            <!-- Notifications will be loaded here -->
                <!--        </div>-->
                <!---->
                <!--        <!-- Footer -->
                <!--        <div class="p-4 bg-gray-50 border-t border-gray-100">-->
                <!--            <button class="w-full py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors duration-300">-->
                <!--                View all notifications-->
                <!--            </button>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->

            </div>

            <!-- Main Content Area -->
            <main class="pb-20 lg:pb-5"> <!-- Added padding bottom for mobile nav -->
                <div class="space-y-8">
                    <div class="flex justify-between items-center flex-wrap gap-4">
                        <div>
                            <h2 class="text-2xl lg:text-3xl font-semibold text-gray-800 mb-1">Available Equipment</h2>
                            <p class="text-gray-500">Browse and borrow available equipment</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5" id="equipmentList">
                        <!-- Equipment cards will be loaded here -->
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="flex justify-center gap-4 mt-8">
                        <!-- Pagination links will be loaded here -->
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php
    // Include the borrow modal and details modal
    displayBorrowModal();
    displayDetailsModal();
    ?>

    <script>
        let currentPage = 1;
        const itemsPerPage = 3;
        let searchQuery = '';
        let categoryFilter = '';

        // Fetch equipment data from the server
        function fetchEquipment(page) {
            $.ajax({
                url: '../user/pagination.php',
                type: 'GET',
                data: {
                    action: 'paginate',
                    page: page,
                    items_per_page: itemsPerPage,
                    category: categoryFilter,
                    search_query: searchQuery
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    document.getElementById('equipmentList').innerHTML = data.html;
                    document.getElementById('pagination').innerHTML = data.pagination;
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('An error occurred while fetching equipment.');
                }
            });
        }

        // Initial fetch
        document.addEventListener('DOMContentLoaded', () => {
            fetchEquipment(currentPage);
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            searchQuery = e.target.value;
            currentPage = 1;
            fetchEquipment(currentPage);
        });

        // Category filter functionality
        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            categoryFilter = e.target.value;
            currentPage = 1;
            fetchEquipment(currentPage);
        });

        function showBorrowModal(equipmentId) {
            // Show loading state
            document.getElementById('borrowModal').style.display = 'flex';
            document.getElementById('previewName').textContent = 'Loading...';

            // Fetch equipment details
            fetch('../inventory/get_equipment.php?id=' + equipmentId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    // Update modal with equipment details
                    document.getElementById('equipmentId').value = data.id;
                    document.getElementById('previewImage').src = data.image_path;
                    document.getElementById('previewName').textContent = data.name;
                    document.getElementById('previewId').textContent = 'ID: ' + data.id;

                    // Set date constraints
                    const today = new Date().toISOString().split('T')[0];
                    const maxDate = new Date();
                    maxDate.setDate(maxDate.getDate() + data.max_borrow_days);

                    const borrowDateInput = document.getElementById('borrowDate');
                    const returnDateInput = document.getElementById('returnDate');

                    borrowDateInput.min = today;
                    borrowDateInput.max = maxDate.toISOString().split('T')[0];
                    returnDateInput.min = today;
                    returnDateInput.max = maxDate.toISOString().split('T')[0];

                    // Populate unit numbers
                    const unitSelect = document.getElementById('unit');
                    unitSelect.innerHTML = ''; // Clear previous options
                    data.units.forEach(unit => {
                        const option = document.createElement('option');
                        option.value = unit;
                        option.textContent = unit;
                        unitSelect.appendChild(option);
                    });

                    // Show modal with animation
                    setTimeout(() => {
                        const modal = document.getElementById('borrowModal');
                        modal.classList.add('opacity-100');
                        modal.querySelector('.transform').classList.remove('-translate-y-5');
                    }, 10);
                })
                .catch(error => {
                    alert('Error loading equipment details: ' + error.message);
                    closeBorrowModal();
                });
        }


        // Notification functionality
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationCount = document.getElementById('notificationCount');
        let isNotificationOpen = false;

        // Toggle notification dropdown
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            isNotificationOpen = !isNotificationOpen;
            if (isNotificationOpen) {
                notificationDropdown.classList.remove('hidden');
                fetchNotifications();
                markNotificationsAsRead(); // Mark notifications as read when opened
            } else {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (isNotificationOpen && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
                isNotificationOpen = false;
            }
        });

        // Prevent dropdown from closing when clicking inside it
        notificationDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Close button functionality
        const closeNotificationBtn = notificationDropdown.querySelector('.bx-x').parentElement;
        closeNotificationBtn.addEventListener('click', () => {
            notificationDropdown.classList.add('hidden');
            isNotificationOpen = false;
        });

        // Function to fetch and display notifications
        function fetchNotifications() {
            $.ajax({
                url: '../user/get.php',
                type: 'GET',
                dataType: 'json', // Automatically parse JSON
                success: function(data) { // Directly receive parsed data
                    updateNotificationList(data.notifications);
                    updateNotificationCount(data.total_notifications);
                },
                error: function() {
                    alert('An error occurred while fetching notifications.');
                }
            });
        }

        // Function to update the notification list
        function updateNotificationList(notifications) {
            const notificationList = document.getElementById('notificationList');
            notificationList.innerHTML = ''; // Clear existing notifications

            notifications.forEach(notification => {
                const notificationElement = document.createElement('div');
                notificationElement.className = 'p-4 hover:bg-gray-50 border-b border-gray-100 cursor-pointer transition-colors duration-300';
                notificationElement.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="bg-green-50 p-2 rounded-full">
                    <i class='bx bx-check-circle text-green-500 text-xl'></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800 font-medium mb-1">${notification.message}</p>
                    <span class="text-xs text-gray-500">${notification.created_at}</span>
                </div>
            </div>
        `;
                notificationList.appendChild(notificationElement);
            });
        }

        // Function to update the notification count
        function updateNotificationCount(count) {
            if (count > 0) {
                notificationCount.textContent = count;
                notificationCount.classList.remove('hidden');
            } else {
                notificationCount.classList.add('hidden');
            }
        }

        // Function to mark notifications as read
        function markNotificationsAsRead() {
            $.ajax({
                url: '../user/get.php?mark_as_read=1',
                type: 'GET',
                dataType: 'json', // Automatically parse JSON
                success: function(data) { // Directly receive parsed data
                    updateNotificationCount(data.total_notifications);
                },
                error: function() {
                    alert('An error occurred while marking notifications as read.');
                }
            });
        }

        function longPollNotifications() {
            setTimeout(() => {
                $.ajax({
                    url: '../user/get.php',
                    type: 'GET',
                    dataType: 'json', // Automatically parse JSON
                    success: function(data) { // Directly receive parsed data
                        updateNotificationCount(data.total_notifications);
                        longPollNotifications(); // Recursive call
                    },
                    error: function() {
                        alert('An error occurred while fetching notifications.');
                        longPollNotifications(); // Retry on error
                    }
                });
            }, 5000); // Poll every 5 seconds
        }
        // Start long polling
        longPollNotifications();
    </script>
</body>

</html>