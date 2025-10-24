<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_SESSION["user_id"])) {
    session_unset();
    session_destroy();
    echo json_encode([
        "status" => "sukses",
        "message" => "Logout Berhasil!"
    ]);
    exit;
}
echo json_encode([
    "status" => "error",
    "message" => "Anda Tidak Memiliki Akses"
]);
exit;