<?php
require '../connect_db.php';
require '../php-config.php';
session_start();

if (isset($_SESSION["user_id"])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $feed_id = $_GET["feed_id"];
        $user_id = $_SESSION["user_id"];

        if (!$feed_id || !$user_id) {
            echo json_encode(["status" => "error", "message" => "feed_id dan user_id wajib diisi"]);
            exit;
        }

        // Cek apakah user sudah like feed ini
        $check_stmt = $conn->prepare("SELECT user_id FROM Feed_Likes WHERE feed_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $feed_id, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Unlike
            $unlike_stmt = $conn->prepare("DELETE FROM Feed_Likes WHERE feed_id = ? AND user_id = ?");
            $unlike_stmt->bind_param("ii", $feed_id, $user_id);
            $unlike_stmt->execute();
            $unlike_stmt->close();

            // Kurangi poin user 100 di tabel User_Progress
            $update_point = $conn->prepare("UPDATE User_Progress SET points = points - 100 WHERE user_id = ?");
            $update_point->bind_param("i", $user_id);
            $update_point->execute();
            $update_point->close();

            $action = false;
        } else {
            // Like
            $like_stmt = $conn->prepare("INSERT INTO Feed_Likes (feed_id, user_id) VALUES (?, ?)");
            $like_stmt->bind_param("ii", $feed_id, $user_id);
            $like_stmt->execute();
            $like_stmt->close();

            // Tambahkan poin user 100 di tabel User_Progress
            $update_point = $conn->prepare("UPDATE User_Progress SET points = points + 100 WHERE user_id = ?");
            $update_point->bind_param("i", $user_id);
            $update_point->execute();
            $update_point->close();

            $action = true;
        }

        $check_stmt->close();

        echo json_encode([
            "status" => "success",
            "action" => $action,
        ]);
        exit;
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Need Login",
    ]);
    exit;
}
