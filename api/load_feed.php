<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT f.feed_id, f.content, f.image, CONVERT_TZ(f.publish_at, '+00:00', '+07:00'), u.email, up.fullname
            FROM Feed f
            JOIN Users u ON f.user_id = u.user_id
            JOIN UserProfile up ON u.user_id = up.user_id
            ORDER BY f.publish_at DESC";

    //Query Ambil Data Feed
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($feed_id, $content, $image, $publish_at, $email, $fullname);


    $feeds = [];
    while ($stmt->fetch()) {
        $feeds[] = [
            'feed_id' => $feed_id,
            'content' => $content,
            'image' => $image,
            'publish_at' => $publish_at,
            'email' => $email,
            'fullname' => $fullname,
        ];

    }
    $stmt->close();


    $liked = false;
    $i = 1;
    $length = count($feeds);
    $j = $length - 1;



    $user_id = $_SESSION["user_id"] ? $_SESSION["user_id"] : null;
    while ($i <= $length) {

        //Query apakah user like feed_id ke ?
        $check_stmt = $conn->prepare("SELECT user_id FROM Feed_Likes WHERE feed_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $i, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $liked = true;
        } else {
            $liked = false;
        }
        $check_stmt->close();


        $count_stmt = $conn->prepare("SELECT COUNT(*) FROM Feed_Likes WHERE feed_id = ?");
        $count_stmt->bind_param("i", $i);
        $count_stmt->execute();
        $count_stmt->bind_result($total_likes);
        $count_stmt->fetch();
        $count_stmt->close();

        $count_stmt = $conn->prepare("SELECT COUNT(*) FROM Feed_Comment WHERE feed_id = ?");
        $count_stmt->bind_param("i", $i);
        $count_stmt->execute();
        $count_stmt->bind_result($total_comment);
        $count_stmt->fetch();
        $count_stmt->close();

        $feeds[$j]["user_data"]["liked"] = $liked;
        $feeds[$j]["user_data"]["user_id"] = $user_id;
        $feeds[$j]["total_likes"] = $total_likes;
        $feeds[$j]["total_comment"] = $total_comment;

        $i++;
        $j--;

    }

    echo json_encode($feeds);

    exit;
}