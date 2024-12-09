<?php
require_once '../classes/database.class.php';
require_once '../classes/equipment.class.php';


$equipment = new Equipment();
$categories = $equipment->fetchCategory();

echo json_encode($categories);
?>