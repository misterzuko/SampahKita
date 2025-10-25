<?php
session_start();
header('Content-Type: application/json');

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "sukses",
        "user_id" => $_SESSION['user_id'],
        "email" => $_SESSION['email'],
        "session_id" => session_id()
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Belum login"
    ]);
}
?>
