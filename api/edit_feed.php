<?php
require '../connect_db.php';
require '../php-config.php';
session_start();
header('Content-Type: application/json');

// Pastikan user login
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User belum login"]);
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $feed_id = $_GET['feed_id'] ?? null;
    $role = $_SESSION["role"] ?? null;

    if (!$feed_id) {
        echo json_encode(["status" => "error", "message" => "feed_id tidak ditemukan"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT feed_id, user_id, content, publish_at, image FROM Feed WHERE feed_id = ?");
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
            echo json_encode(["status" => "error", "message" => "Tidak diizinkan mengedit feed ini"]);
            exit;
        }
    }

    echo json_encode([
        "status" => "sukses",
        "data" => [
            "feed_id" => $feed["feed_id"],
            "content" => $feed["content"],
            "image" => $feed["image"],
            "publish_at" => $feed["publish_at"]
        ]
    ]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $feed_id = $_GET["feed_id"] ?? null;
    $new_content = $_POST["content"] ?? null;
    $role = $_SESSION["role"] ?? null;

    if (!$feed_id || !$new_content) {
        echo json_encode(["status" => "error", "message" => "Parameter tidak lengkap" . $feed_id . $new_content]);
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, image FROM Feed WHERE feed_id = ?");
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
            echo json_encode(["status" => "error", "message" => "Tidak diizinkan mengedit feed ini"]);
            exit;
        }
    }

    $img_path = $feed["image"];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/feeds/" . $feed_id . "/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($file_ext, $allowed_ext)) {
            echo json_encode(["status" => "error", "message" => "Format gambar tidak valid"]);
            exit;
        }
        $new_name = "feed-edit-" . $feed_id . "." . $file_ext;
        $destination = $upload_dir . $new_name;

        if ($img_path && file_exists("../" . $img_path)) {
            unlink("../" . $img_path);
        }

        if (!move_uploaded_file($file_tmp, $destination)) {
            echo json_encode(["status" => "error", "message" => "Gagal upload gambar"]);
            exit;
        }

        $img_path = "uploads/feeds/" . $feed_id . "/" . $new_name;
    }

    $update = $conn->prepare("UPDATE Feed SET content = ?, image = ?, publish_at = NOW() WHERE feed_id = ?");
    $update->bind_param("sss", $new_content, $img_path, $feed_id);

    if ($update->execute()) {
        echo json_encode(["status" => "sukses", "message" => "Feed berhasil diperbarui"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal memperbarui feed"]);
    }
    exit;
}

echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
exit;
?>