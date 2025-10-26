<?php
require '../connect_db.php';
require '../php-config.php';
session_start();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Parameter user_id wajib diisi"]);
        exit;
    }

    $stmt = $conn->prepare(
        "
        SELECT 
            scan_id, 
            user_id,
            image_path, 
            result, 
            DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') AS created_at
        FROM User_Scan_History 
        WHERE user_id = ? 
        ORDER BY created_at DESC
        ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }

    echo json_encode([
        "status" => "sukses",
        "data" => $history
    ]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["status" => "error", "message" => "User belum login"]);
        exit;
    }

    $user_id = $_SESSION["user_id"];

    if (!isset($_FILES['image'])) {
        echo json_encode(["status" => "error", "message" => "File gambar tidak ditemukan"]);
        exit;
    }

    $result_text = $_GET['result'] ?? null;
    if (!$result_text) {
        echo json_encode(["status" => "error", "message" => "Hasil scan belum diisi"]);
        exit;
    }

    $upload_dir = "../uploads/scans/$user_id/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_tmp = $_FILES['image']['tmp_name'];
    $file_name = "scan_" . date("Ymd_His") . ".png";
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($file_tmp, $file_path)) {
        echo json_encode(["status" => "error", "message" => "Gagal mengupload gambar"]);
        exit;
    }

    $relative_path = "uploads/scans/$user_id/" . $file_name;
    $stmt = $conn->prepare("INSERT INTO User_Scan_History (user_id, image_path, result) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $relative_path, $result_text);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        echo json_encode(["status" => "sukses", "message" => "Hasil scan berhasil disimpan"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data scan"]);
    }
    exit;
}

echo json_encode(["status" => "error", "message" => "Metode request tidak valid"]);
exit;
?>