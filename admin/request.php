<?php
require_once 'session_check.php';
require_once '../classes/database.class.php';
require_once '../classes/borrowing.class.php';
require_once '../classes/equipment.class.php';

$conn = new Database();
$borrowing = new Borrowing();
$equipment = new Equipment();

// Handle approve and reject requests
require_once '../request/handle_borrow_request.php';

// Fetch borrow requests
$borrow_requests = $borrowing->fetchBorrowRequests();

// Fetch categories for the dropdown
$categories = $equipment->fetchCategory();
?>
<?php require_once '../includes/header.php'; ?>

<body class="bg-gray-100 flex min-h-screen relative overflow-x-hidden">
    <!-- Sidebar -->
    <?php require_once '../includes/side_bar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 ml-[21rem] w-[calc(100%-16rem)] transition-all duration-300 ease-in-out">
        <!-- Navigation -->
        <?php require_once '../includes/top_nav.php'; ?>

        <?php require_once '../request/view_request.php'; ?>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>

</html>