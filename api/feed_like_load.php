<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    //DUMMY
    $feed_id;

    //Quert Hitung Total Likes
    $count_stmt = $conn->prepare("SELECT COUNT(*) FROM Feed_Likes WHERE feed_id = ?");
    $count_stmt->bind_param("i", $feed_id);
    $count_stmt->execute();
    $count_stmt->bind_result($total_likes);
    $count_stmt->fetch();
    $count_stmt->close();
    echo json_encode([
        "feed_id" => $feed_id,
        "total_liked" => $total_likes
    ]);
}
