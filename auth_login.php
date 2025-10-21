<?php

require 'connect_db.php';

//LOGIN
if ($_SERVER['REQUEST_METHOD'] == "POST") {
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

    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $user, $hash);
        mysqli_stmt_fetch($stmt);

        if (password_verify($password, $hash)) {
            $data = [
                "status" => "sukses",
                "message" => "Login Berhasil"
            ];
            echo json_encode($data);
            mysqli_stmt_close($stmt);
            exit;
        }
        $data = [
            "status" => "erorr",
            "message" => "Username Atau Passsword Salah"
        ];
        echo json_encode($data);
        mysqli_stmt_close($stmt);
        exit;
    }
}


?>