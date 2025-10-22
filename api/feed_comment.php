<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
//Ambil Data Komentar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    //dummy
    $feed_id=1;


    $sql = "
        SELECT c.comment_id AS comment_id, c.content, c.commented_at,
               u.user_id, u.email, up.fullname
        FROM Feed_Comment c
        JOIN Users u ON c.user_id = u.user_id
        JOIN UserProfile up ON u.user_id = up.user_id
        WHERE c.feed_id = ?
        ORDER BY c.commented_at DESC
    ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $feed_id);
        $stmt->execute();

        $result = $stmt->get_result();

        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "message" => "Berhasil Memuat Komentar",
            "data" => $comments
        ]);

        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal Memuat Komentar: " . $conn->error
        ]);
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    //DUMMY 
    $user_id;
    $feed_id;
    $comment = "AAOwkoakw mampus lu";

    if (!$comment) {
        echo json_encode([
            "status" => "error",
            "message" => "Komentar Kosong!"
        ]);
        exit;
    }

    $sql = "INSERT INTO Comments (feed_id, user_id, comment) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iis", $feed_id, $user_id, $comment);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Komentar berhasil ditambahkan",
                "comment_id" => $stmt->insert_id
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menambahkan komentar: " . $conn->error
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Prepare failed: " . $conn->error
        ]);
    }

    exit;
}