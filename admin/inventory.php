<?php
require_once 'session_check.php';
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';


$equipment = new Equipment();
$equipment_list = $equipment->showAll();
$categories = $equipment->fetchCategory();
?>
<?php require_once '../includes/header.php' ?>

</style>

<body class="bg-gray-100 min-h-screen flex relative overflow-x-hidden">
    <!-- Sidebar -->
    <?php require_once '../includes/side_bar.php' ?>
    <!-- Main Container -->
    <div class="ml-[21rem] flex-1 w-[calc(100%-16rem)]">
        <!-- Navigation -->
        <?php require_once '../includes/top_nav.php' ?>
        <!-- Main Content -->
        <?php require_once '../inventory/view_inventory.php' ?>
    </div>

    <?php require_once '../inventory/add_modal.php' ?>
    <?php require_once '../inventory/edit_modal.php' ?>
    <!-- Scripts -->
    <?php require_once '../includes/footer.php' ?>
</body>

</html>