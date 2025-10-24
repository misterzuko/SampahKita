<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
session_start();

if ($_SESSION["user_id"]) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $input = json_decode(file_get_contents('php://input'), true);

        // DUMMY
        $user_id = $_SESSION["user_id"];
        $content = $input['content'] ?? null;
        $img = "";


        if (!$content) {
            echo json_encode([
                "status" => "error",
                "message" => "Isi konten terlebih dahulu!"
            ]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO Feed (user_id, content, image) VALUES (?,?,?)");
        $stmt->bind_param("iss", $user_id, $content, $img);
        $stmt->execute();
        $stmt->close();

        $stmt_updt = $conn->prepare("UPDATE User_Progress SET points = points + 1000 WHERE user_id = ?");
        $stmt_updt->bind_param("i", $user_id);
        $result = $stmt_updt->execute();
        $stmt_updt->close();

        if ($result) {
            echo json_encode([
                "status" => "sukses",
                "message" => "Konten telah diposting"
            ]);
            
            exit;
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Terjadi kesalahan saat menyimpan data: " . $conn->error
            ]);
            exit;
        }
    }
}