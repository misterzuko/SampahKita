<?php
require '../connect_db.php';
require '../php-config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {


    $user_id = $_SESSION["user_id"];

    // Ambil data user progress dari tabel User_Progress adws
    $stmt = $conn->prepare("SELECT points, level, tier, total_recycle FROM User_Progress WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    //Level kalkuls (Per level 2000 points)
    if ($result->num_rows > 0) {
        $progress = $result->fetch_assoc();
        $points = $progress['points'];
        $total_recycle = $progress['total_recycle'];
        $level = floor($points / 2000) + 1;
        $title = "";

        if ($level < 5) {
            $tier = "Bronze";
            $title = "Eco Explorer";
        } elseif ($level < 10) {
            $tier = "Silver";
            $title = "Eco Guardian";
        } elseif ($level < 15) {
            $tier = "Gold";
            $title = "The Eco Inovator";
        } elseif ($level < 20) {
            $tier = "Platinum";
            $title = "The Eco Legend";
        } else {
            $tier = "Diamonds";
            $tier = "Guardian of the Earth";
        }

        //Cek apakah level/tier sinkron with db
        if ($level != $progress['level'] || $tier != $progress['tier']) {
            $update = $conn->prepare("UPDATE User_Progress SET level = ?, tier = ? WHERE user_id = ?");
            $update->bind_param("isi", $level, $tier, $user_id);
            $update->execute();
            $update->close();
        }
        

        $sql = "
                SELECT rank, user_id, fullname, points
                FROM (
                SELECT 
                    u.user_id,
                    up.fullname,
                    p.points,
                    RANK() OVER (ORDER BY p.points DESC) AS rank
                FROM User_Progress p
                JOIN Users u ON p.user_id = u.user_id
                JOIN User_Profile up ON u.user_id = up.user_id
                ) ranked
                WHERE user_id = ?;
            ";

        $leaderboard = $conn->prepare($sql);
        $leaderboard->bind_param("i", $user_id);
        $leaderboard->execute();
        $result = $leaderboard->get_result();
        $qresult = $result->fetch_assoc();
        $rank = $qresult["rank"];


        echo json_encode([
            "status" => "sukses",
            "user_id" => $user_id,
            "points" => $points,
            "level" => $level,
            "tier" => $tier,
            "title" => $title,
            "rank" => $rank,
            "total_recycle" => $total_recycle
        ]);
        exit;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Progress user tidak ditemukan"
        ]);
        exit;
    }

}

echo json_encode([
    "status" => "error",
    "message" => "Anda Tidak Memili Akses"
]);
exit;