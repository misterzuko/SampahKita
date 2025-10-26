<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');
session_start();

if (isset($_GET["feed_id"])) {
    $feed_id = intval($_GET["feed_id"]);

    // Query detail postingan
    $sql = "SELECT 
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
            WHERE f.feed_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $feed_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Feed not found"
        ]);
        exit;
    }

    $feed = $result->fetch_assoc();
    $stmt->close();

    // Total Likes
    $stmtLikes = $conn->prepare("SELECT COUNT(*) AS total_likes FROM Feed_Likes WHERE feed_id = ?");
    $stmtLikes->bind_param("i", $feed_id);
    $stmtLikes->execute();
    $likesResult = $stmtLikes->get_result()->fetch_assoc();
    $feed["total_likes"] = intval($likesResult["total_likes"]);
    $stmtLikes->close();

    // Total Comments
    $stmtComments = $conn->prepare("SELECT COUNT(*) AS total_comments FROM Feed_Comment WHERE feed_id = ?");
    $stmtComments->bind_param("i", $feed_id);
    $stmtComments->execute();
    $commentsResult = $stmtComments->get_result()->fetch_assoc();
    $feed["total_comments"] = intval($commentsResult["total_comments"]);
    $stmtComments->close();

    // Daftar Komentar
    $sqlComments = "
        SELECT 
            fc.comment_id,
            fc.content,
            CONVERT_TZ(fc.commented_at, '+00:00', '+07:00') AS commented_at,
            u.user_id,
            up.fullname
        FROM Feed_Comment fc
        JOIN Users u ON fc.user_id = u.user_id
        JOIN User_Profile up ON u.user_id = up.user_id
        WHERE fc.feed_id = ?
        ORDER BY fc.commented_at ASC
    ";

    $stmtCommentList = $conn->prepare($sqlComments);
    $stmtCommentList->bind_param("i", $feed_id);
    $stmtCommentList->execute();
    $resultComments = $stmtCommentList->get_result();

    $comments = [];
    while ($row = $resultComments->fetch_assoc()) {
        $comments[] = [
            "comment_id" => $row["comment_id"],
            "content" => $row["content"],
            "commented_at" => $row["commented_at"],
            "user_id" => $row["user_id"],
            "fullname" => $row["fullname"]
        ];
    }
    $stmtCommentList->close();
    $feed["liked"] = false;
    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $checkLike = $conn->prepare("SELECT 1 FROM Feed_Likes WHERE feed_id = ? AND user_id = ?");
        $checkLike->bind_param("ii", $feed_id, $user_id);
        $checkLike->execute();
        $checkLike->store_result();
        $feed["liked"] = $checkLike->num_rows > 0;
        $checkLike->close();
    }
    echo json_encode([
        "status" => "success",
        "feed" => $feed,
        "comments" => $comments
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT f.feed_id, f.content, f.image, CONVERT_TZ(f.publish_at, '+00:00', '+07:00'), u.user_id, up.fullname
            FROM Feed f
            JOIN Users u ON f.user_id = u.user_id
            JOIN User_Profile up ON u.user_id = up.user_id
            ORDER BY f.publish_at DESC";

    //Query Ambil Data Feed
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($feed_id, $content, $image, $publish_at, $user_id, $fullname);


    $feeds = [];
    while ($stmt->fetch()) {
        $feeds[] = [
            'user_id' => $user_id,
            'feed_id' => $feed_id,
            'content' => $content,
            'image' => $image,
            'publish_at' => $publish_at,
            'fullname' => $fullname,
        ];

    }
    $stmt->close();


    $liked = false;
    $i = 1;
    $length = count($feeds);
    $j = $length - 1;



    $user_id = $_SESSION["user_id"] ?? null;
    $role = $_SESSION["role"] ?? null;
    
    while ($i <= $length) {
        $countliked = $feeds[$j]["feed_id"];
        //Query apakah user like feed_id ke ?
        $check_stmt = $conn->prepare("SELECT user_id FROM Feed_Likes WHERE feed_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $countliked, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $liked = true;
        } else {
            $liked = false;
        }
        $check_stmt->close();


        $count_stmt = $conn->prepare("SELECT COUNT(*) FROM Feed_Likes WHERE feed_id = ?");
        $count_stmt->bind_param("i", $countliked);
        $count_stmt->execute();
        $count_stmt->bind_result($total_likes);
        $count_stmt->fetch();
        $count_stmt->close();

        $countcmnt_stmt = $conn->prepare("SELECT COUNT(*) FROM Feed_Comment WHERE feed_id = ?");
        $countcmnt_stmt->bind_param("i", $countliked);
        $countcmnt_stmt->execute();
        $countcmnt_stmt->bind_result($total_comment);
        $countcmnt_stmt->fetch();
        $countcmnt_stmt->close();

        $feeds[$j]["user_data"]["liked"] = $liked;
        $feeds[$j]["user_data"]["user_id"] = $user_id;
        $feeds[$j]["user_data"]["role"] = $role;
        $feeds[$j]["total_likes"] = $total_likes;
        $feeds[$j]["total_comment"] = $total_comment;

        $i++;
        $j--;

    }

    echo json_encode($feeds);

    exit;
}
echo json_encode([
    "status" => "error",
    "message" => "Anda tidak memiliki akses"
]);
exit;