<?php
require '../connect_db.php';
require '../php-config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User belum login"]);
    exit;
}

$user_id = $_SESSION["user_id"];


if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    $input = json_decode(file_get_contents("php://input"), true);
    $feed_id = $input["feed_id"] ?? null;
    $role = $_SESSION["role"] ?? null;

    if (!$feed_id) {
        echo json_encode(["status" => "error", "message" => "feed_id tidak ditemukan"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id FROM Feed WHERE feed_id = ?");
    $stmt->bind_param("s", $feed_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Feed tidak ditemukan"]);
        exit;
    }

    $feed = $result->fetch_assoc();
    if ($role != "admin") {
        if ($feed["user_id"] != $user_id) {
            echo json_encode(["status" => "error", "message" => "Tidak diizinkan menghapus feed ini"]);
            exit;
        }
    }

    $delete = $conn->prepare("DELETE FROM Feed WHERE feed_id = ?");
    $delete->bind_param("s", $feed_id);

    if ($delete->execute()) {
        echo json_encode(["status" => "sukses", "message" => "Feed berhasil dihapus"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menghapus feed"]);
    }
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
    exit;
}
?>