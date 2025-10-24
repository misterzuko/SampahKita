<?php
require '../connect_db.php';
require '../php-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // DUMMY data
    $fullname = $input["fullname"];
    $email = $input["email"];
    $password = $input["password"];

    if (!$email || !$password) {
        echo json_encode([
            "status" => "error",
            "message" => "Email atau Password kosong"
        ]);
        exit;
    }

    $check_stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Email telah digunakan atau terdaftar"
        ]);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert ke tabel Users
    $insert_stmt = $conn->prepare("INSERT INTO Users (email, password) VALUES (?, ?)");
    $insert_stmt->bind_param("ss", $email, $password_hash);

    if ($insert_stmt->execute()) {

        $user_id = $conn->insert_id;

        // Insert ke tabel User_Profile
        $profile_stmt = $conn->prepare("INSERT INTO User_Profile (user_id, fullname) VALUES (?, ?)");
        $profile_stmt->bind_param("is", $user_id, $fullname);
        $profile_stmt->execute();
        $profile_stmt->close();

        // Insert ke tabel User_Progress
        $up_stmt = $conn->prepare("INSERT INTO User_Progress (user_id) VALUES (?)");
        $up_stmt->bind_param("i", $user_id);
        $up_stmt->execute();
        $up_stmt->close();

        echo json_encode([
            "status" => "sukses",
            "message" => "Pengguna sukses terdaftar"
        ]);

        $insert_stmt->close();
        exit;
    }

    echo json_encode([
        "status" => "error",
        "message" => "Terjadi kesalahan saat menyimpan data: " . $conn->error
    ]);
    $insert_stmt->close();
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "Anda Tidak Memili Akses"
]);
exit;
