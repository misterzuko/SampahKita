<?php
require '../connect_db.php';
require '../php-config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $input = json_decode(file_get_contents('php://input'), true);

    // DUMMY
    $email = "daniro@admin.com";
    $password = "admin123";

    if (!$email || !$password) {
        echo json_encode([
            "status" => "error",
            "message" => "Email atau Password Kosong"
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT user_id, email, password FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows) {
            $stmt->bind_result($id, $user, $hash);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                echo json_encode([
                    "status" => "sukses",
                    "message" => "Login Berhasil"
                ]);
                $stmt->close();
                exit;
            }
        }

        echo json_encode([
            "status" => "error",
            "message" => "Email atau Password Salah"
        ]);
        $stmt->close();
        exit;

    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "SQL ERROR: " . $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode([
    "status" => "error",
    "message" => "Anda Tidak Memili Akses"
]);
exit;
?>