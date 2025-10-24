<?php
require '../connect_db.php';
require '../php-config.php';
session_start();

header("Content-Type: application/json");

$sql = "
  SELECT 
    u.user_id,
    up.fullname,
    p.points,
    p.level,
    p.tier
  FROM User_Progress p
  JOIN Users u ON p.user_id = u.user_id
  JOIN User_Profile up ON u.user_id = up.user_id
  ORDER BY p.points DESC
  LIMIT 20
";

$result = $conn->query($sql);
$leaderboard = [];
if ($result->num_rows > 0) {
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = [
            "rank" => $rank++,
            "user_id" => $row['user_id'],
            "fullname" => $row['fullname'],
            "points" => intval($row['points']),
            "level" => intval($row['level']),
            "tier" => $row['tier']
        ];
    }

    echo json_encode([
        "status" => "success",
        "leaderboard" => $leaderboard
    ]);
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "Belum ada data leaderboard."
]);
exit;
