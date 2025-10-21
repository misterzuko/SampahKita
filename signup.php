<?php
header('Content-Type: application/json');
require 'connect_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!$username || !$password) {
        $data = [
            "status" => "error",
            "message" => "Username atau Password Kosong"
        ];
        echo json_encode($data);
        exit;
    }

    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $data = [
            "status" => "error",
            "message" => "Username Sudah Terdaftar"
        ];
        echo json_encode($data);
        mysqli_stmt_close($check_stmt);
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Hash password sebelum simpan
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Simpan data ke database
    $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($insert_stmt, "ss", $username, $password_hash);

    if (mysqli_stmt_execute($insert_stmt)) {
        $data = [
            "status" => "sukses",
            "message" => "Pengguna Telah Terdaftar"
        ];
        echo json_encode($data);
    } else {
        $data = [
            "status" => "error",
            "message" => "Terjadi kesalahan saat menyimpan data: " . mysqli_error($conn)
        ];
        echo json_encode($data);
    }

    mysqli_stmt_close($insert_stmt);
}
?>