<?php 
session_start();
include "../vendor/autoload.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    unset($_SESSION['auth']);
    session_destroy();

    echo json_encode([
        'status' => 200,
        'message' => 'Logout successfully'
    ]);
}