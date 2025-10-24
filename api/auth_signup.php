<?php
require '../connect_db.php';
require '../php-config.php';

header('Content-Type: application/json');
session_start();

if(isset($_SESSION["user_id"])){
    header("Location: ../src/page/bank_sampah");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // DUMMY data
    $fullname = "Baskoro Adi Widagdo";
    $email = "baskoro@admin.com";
    $password = "admin123";

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

        // Insert ke tabel UserProfile
        $profile_stmt = $conn->prepare("INSERT INTO UserProfile (user_id, fullname) VALUES (?, ?)");
        $profile_stmt->bind_param("is", $user_id, $fullname);
        $profile_stmt->execute();
        $profile_stmt->close();

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
