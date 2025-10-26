<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
session_start();
if ($_SESSION["user_id"]) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $feed_id = $_GET['feed_id'];

        if (!$feed_id) {
            echo json_encode([
                "status" => "error",
                "message" => "Feed ID tidak ditemukan"
            ]);
            exit;
        }

        $sql = "
        SELECT 
            c.comment_id,c.feed_id,
            c.content,CONVERT_TZ(c.commented_at, '+00:00', '+07:00') AS commented_at,
            u.user_id,u.email,up.fullname
        FROM Feed_Comment c
        JOIN Users u ON c.user_id = u.user_id
        JOIN User_Profile up ON u.user_id = up.user_id
        WHERE c.feed_id = ?
        ORDER BY c.commented_at DESC
        ";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $feed_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $comments = [];
            while ($row = $result->fetch_assoc()) {
                $comments[] = [
                    "comment_id" => $row["comment_id"],
                    "feed_id" => $row["feed_id"],
                    "user_id" => $row["user_id"],
                    "fullname" => $row["fullname"],
                    "email" => $row["email"],
                    "content" => $row["content"],
                    "commented_at" => $row["commented_at"]
                ];
            }

            echo json_encode([
                "status" => "success",
                "message" => "Berhasil memuat komentar",
                "feed_id" => $feed_id,
                "comments" => $comments
            ]);

            $stmt->close();
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal memuat komentar: " . $conn->error
            ]);
        }
        exit;
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $user_id = $_SESSION["user_id"];
        $feed_id = $input['feed_id'] ?? null;
        $comment = $input["komentar"] ?? null;

        $comment = trim((string) $comment);
        if ($comment === '' || !$feed_id) {
            echo json_encode([
                "status" => "error",
                "message" => "Feed ID dan komentar wajib diisi"
            ]);
            exit;
        }
        $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
        $stmt = $conn->prepare("INSERT INTO Feed_Comment (feed_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $feed_id, $user_id, $comment);

        if ($stmt->execute()) {
            $conn->query("UPDATE User_Progress SET points = points + 1000 WHERE user_id = $user_id");

            echo json_encode([
                "status" => "success",
                "message" => "Komentar berhasil ditambahkan"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menambahkan komentar: " . $stmt->error
            ]);
        }

        $stmt->close();
        exit;
    }

}

echo json_encode([
    "status" => "error",
    "message" => "Anda tidak meiliki akses"
]);

exit;