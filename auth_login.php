<?php

require 'connect_db.php';
require 'php-config.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $input = json_decode(file_get_contents('php://input'), true);

    $email = $input['email'];
    $password = $input['password'];

    if (!$email || !$password) {
        $data = [
            "status" => "error",
            "message" => "Email atau Password Kosong"
        ];
        echo json_encode($data);
        exit;
    }


    try {
        $stmt = mysqli_prepare($conn, "SELECT id, email, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
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
        }
        $data = [
            "status" => "erorr",
            "message" => "Email Atau Passsword Salah"
        ];
        echo json_encode($data);
        mysqli_stmt_close($stmt);
        exit;

    } catch (mysqli_sql_exception $e) {
        $data = [
            "status" => "erorr",
            "message" => "SQL ERROR: " . $e,
        ];
        echo json_encode($data);
        exit;
    }
}

$data = [
    "status" => "erorr",
    "message" => "Method Not Allowed"
];
echo json_encode($data);
?>