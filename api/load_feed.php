<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT f.feed_id, f.content, f.image, f.publish_at, u.email, up.fullname
            FROM Feed f
            JOIN Users u ON f.user_id = u.user_id
            JOIN UserProfile up ON u.user_id = up.user_id
            ORDER BY f.publish_at DESC";

    //Query Ambil Data Feed
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($feed_id, $description, $image, $publish_at, $email, $fullname);


    $feeds = [];
    while ($stmt->fetch()) {
        $feeds[] = [
            'feed_id' => $feed_id,
            'description' => $description,
            'image' => $image,
            'publish_at' => $publish_at,
            'email' => $email,
            'fullname' => $fullname
        ];
    }

    echo json_encode($feeds);
    $stmt->close();
    exit;
}