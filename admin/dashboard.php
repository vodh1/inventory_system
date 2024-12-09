<?php
require_once 'session_check.php';
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';
require_once '../classes/borrowing.class.php';
require_once '../classes/transaction.class.php';

$db = new Database();
$conn = $db->connect();
require_once '../dashboard/fetch_matrix.php';
?>
<?php require_once '../includes/header.php' ?>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex relative overflow-x-hidden">
    <!-- Sidebar -->
    <?php require_once '../includes/side_bar.php' ?>
    <!-- Main Container -->
    <div class="flex-1 ml-[21rem] transition-all duration-300" id="mainContent">
        <!-- Navigation -->

        <!-- Main Content -->
        <?php require_once '../dashboard/view_dashboard.php' ?>
    </div>
    <script>
        var borrowingTrendsData = <?php echo json_encode($borrowing_trends); ?>;
        var equipmentCategoriesData = <?php echo json_encode($equipment_categories); ?>;
    </script>
    <?php require_once '../includes/footer.php' ?>
</body>

</html>