<?php

require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
session_start();

// Ambil user_id
$user_id = $_GET['user_id'] ?? ($_SESSION['user_id'] ?? null);

if (!$user_id) {
    echo json_encode([
        "status" => "error",
        "message" => "User ID tidak ditemukan."
    ]);
    exit;
}

// Query ambil semua postingan user
$sql = "
    SELECT 
        f.feed_id,
        f.content,
        f.image,
        CONVERT_TZ(f.publish_at, '+00:00', '+07:00') AS publish_at,
        u.user_id,
        u.email,
        up.fullname
    FROM Feed f
    JOIN Users u ON f.user_id = u.user_id
    JOIN User_Profile up ON u.user_id = up.user_id
    WHERE f.user_id = ?
    ORDER BY f.publish_at DESC
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    $fullname = null;
    $email = null;

    while ($row = $result->fetch_assoc()) {
        if (!$fullname)
            $fullname = $row["fullname"];
        if (!$email)
            $email = $row["email"];

        $stmtLikes = $conn->prepare("SELECT COUNT(*) FROM Feed_Likes WHERE feed_id = ?");
        $stmtLikes->bind_param("s", $row["feed_id"]);
        $stmtLikes->execute();
        $stmtLikes->bind_result($total_likes);
        $stmtLikes->fetch();
        $stmtLikes->close();

        $posts[] = [
            "feed_id" => $row["feed_id"],
            "content" => $row["content"],
            "image" => $row["image"],
            "publish_at" => $row["publish_at"],
            "total_likes" => (int) $total_likes
        ];
    }

    echo json_encode([
        "status" => "success",
        "user_id" => $user_id,
        "fullname" => $fullname,
        "email" => $email,
        "total_posts" => count($posts),
        "feeds" => $posts
    ]);

    $stmt->close();
    exit;
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Query gagal dipersiapkan: " . $conn->error
    ]);
}
echo json_encode([
    "status" => "error",
    "message" => "Anda tidak memiliki akses"
]);
exit;
