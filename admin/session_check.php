<?php
session_start();
require_once '../libs/enums.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== Role::Administrator->value) {
    header("Location: ../index.php");
    exit();
}
