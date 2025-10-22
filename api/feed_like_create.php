<?php
require '../connect_db.php';
require '../php-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    //DUMMY
    $feed_id = $input['feed_id'] ?? null;
    $user_id = $input['user_id'] ?? null;

    if (!$feed_id || !$user_id) {
        echo json_encode(["status" => "error", "message" => "feed_id dan user_id wajib diisi"]);
        exit;
    }

    // Apakah Feed Sudah di Like?
    $check_stmt = $conn->prepare("SELECT id FROM feed_likes WHERE feed_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $feed_id, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    // Jika sudah like → lakukan UNLIKE
    if ($check_stmt->num_rows > 0) {
        $unlike_stmt = $conn->prepare("DELETE FROM feed_likes WHERE feed_id = ? AND user_id = ?");
        $unlike_stmt->bind_param("ii", $feed_id, $user_id);
        $unlike_stmt->execute();
        $unlike_stmt->close();

        $action = "Unliked";
        // Jika belum like → lakukan LIKE
    } else {
        $like_stmt = $conn->prepare("INSERT INTO feed_likes (feed_id, user_id) VALUES (?, ?)");
        $like_stmt->bind_param("ii", $feed_id, $user_id);
        $like_stmt->execute();
        $like_stmt->close();

        $action = "Liked";
    }
    $check_stmt->close();

    echo json_encode([
        "status" => "success",
        "action" => $action,
    ]);
    exit;
}