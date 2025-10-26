<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
session_start();

// Pastikan user login
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User belum login"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION["user_id"];
    $content = $_POST['content'] ?? null;

    $content  = htmlspecialchars($content , ENT_QUOTES, 'UTF-8');

    if (!$content) {
        echo json_encode(["status" => "error", "message" => "Isi konten terlebih dahulu!"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Feed (user_id, content, image) VALUES (?, ?, '')");
    $stmt->bind_param("is", $user_id, $content);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Gagal membuat feed"]);
        exit;
    }

    $feed_id = $conn->insert_id;
    $stmt->close();

    $img_path = null;
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

        $new_name = "feed-" . $feed_id . "." . $file_ext;
        $destination = $upload_dir . $new_name;

        if (!move_uploaded_file($file_tmp, $destination)) {
            echo json_encode(["status" => "error", "message" => "Gagal mengupload gambar"]);
            exit;
        }

        $img_path = "uploads/feeds/" . $feed_id . "/" . $new_name;

        $update = $conn->prepare("UPDATE Feed SET image = ? WHERE feed_id = ?");
        $update->bind_param("si", $img_path, $feed_id);
        $update->execute();
        $update->close();
    }

    $stmt_updt = $conn->prepare("UPDATE User_Progress SET points = points + 3000 WHERE user_id = ?");
    $stmt_updt->bind_param("i", $user_id);
    $stmt_updt->execute();
    $stmt_updt->close();

    echo json_encode([
        "status" => "sukses",
        "message" => "Konten berhasil diposting",
        "feed_id" => $feed_id,
        "image" => $img_path ?? null
    ]);
}
echo json_encode([
    "status" => "error",
    "message" => "Anda tidak memiliki akses"
]);
exit;
