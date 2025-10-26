<?php
require '../connect_db.php';
require '../php-config.php';
session_start();

header("Content-Type: application/json");

if (isset($_SESSION["user_id"])) {
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $user_id = $_SESSION["user_id"];
        $sqlLikes = "
        SELECT COUNT(fl.like_id) AS total_likes
        FROM Feed_Likes fl
        JOIN Feed f ON fl.feed_id = f.feed_id
        WHERE f.user_id = ?
        ";
        $sqlComments = "
        SELECT COUNT(fc.comment_id) AS total_comments
        FROM Feed_Comment fc
        JOIN Feed f ON fc.feed_id = f.feed_id
        WHERE f.user_id = ?
        ";

        $sqlPosts = "
        SELECT COUNT(feed_id) AS total_posts
        FROM Feed
        WHERE user_id = ?
        ";

        $stmtLikes = $conn->prepare($sqlLikes);
        $stmtLikes->bind_param("i", $user_id);
        $stmtLikes->execute();
        $resultLikes = $stmtLikes->get_result()->fetch_assoc();
        $totalLikes = $resultLikes['total_likes'] ?? 0;

        $stmtComments = $conn->prepare($sqlComments);
        $stmtComments->bind_param("i", $user_id);
        $stmtComments->execute();
        $resultComments = $stmtComments->get_result()->fetch_assoc();
        $totalComments = $resultComments['total_comments'] ?? 0;

        $stmtPosts = $conn->prepare($sqlPosts);
        $stmtPosts->bind_param("i", $user_id);
        $stmtPosts->execute();
        $resultPosts = $stmtPosts->get_result()->fetch_assoc();
        $totalPosts = $resultPosts['total_posts'] ?? 0;

        echo json_encode([
            "status" => "sukses",
            "user_id" => $user_id,
            "total_posts" => $totalPosts,
            "total_likes" => intval($totalLikes),
            "total_comments" => intval($totalComments)
        ]);
        exit;
    }
}

echo json_encode([
    "status" => "error",
    "message" => "Anda tidak memilik izin"
]);
exit;