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
$role = $_SESSION["role"] ?? null;
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$comment_id = $input["comment_id"] ?? $_POST["comment_id"] ?? $_GET["comment_id"] ?? null;

if (!$comment_id) {
    echo json_encode(["status" => "error", "message" => "Parameter comment_id wajib diisi"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT user_id FROM Feed_Comment WHERE comment_id = ?");
    $stmt->bind_param("s", $comment_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Komentar tidak ditemukan"]);
        exit;
    }

    $comment = $res->fetch_assoc();
    $stmt->close();

    if ($comment["user_id"] != $user_id && $role !== "admin") {
        echo json_encode(["status" => "error", "message" => "Tidak diizinkan menghapus komentar ini"]);
        exit;
    }

    $delete = $conn->prepare("DELETE FROM Feed_Comment WHERE comment_id = ?");
    $delete->bind_param("s", $comment_id);

    
    if ($delete->execute()) {
         $conn->query("UPDATE User_Progress SET points = points - 1000 WHERE user_id = $user_id");

        echo json_encode(["status" => "sukses", "message" => "Komentar berhasil dihapus"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menghapus komentar: " . $conn->error]);
    }
    $delete->close();
    exit;
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan: " . $e->getMessage()]);
    exit;
}
