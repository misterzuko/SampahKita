<?php
require 'connect_db.php';
require 'php-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fullname = $input["nama_lengkap"];
    $email = $input['email'];
    $password = $input['password'];

    if (!$email || !$password) {
        $data = [
            "status" => "error",
            "message" => "email atau Password Kosong"
        ];
        echo json_encode($data);
        exit;
    }

    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $data = [
            "status" => "error",
            "message" => "email Telah Digunakan atau Terdaftar"
        ];
        echo json_encode($data);
        mysqli_stmt_close($check_stmt);
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Hash password sebelum simpan
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Simpan data ke database
    $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (fullname,email, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($insert_stmt, "sss", $fullname, $email, $password_hash);

    if (mysqli_stmt_execute($insert_stmt)) {
        $data = [
            "status" => "sukses",
            "message" => "Pengguna Sukses Terdaftar"
        ];
        echo json_encode($data);
    }

    $data = [
        "status" => "error",
        "message" => "Terjadi kesalahan saat menyimpan data: " . mysqli_error($conn)
    ];
    echo json_encode($data);
    mysqli_stmt_close($insert_stmt);
}

$data = [
    "status" => "erorr",
    "message" => "Method Not Allowed"
];
echo json_encode($data, JSON_PRETTY_PRINT);
?>