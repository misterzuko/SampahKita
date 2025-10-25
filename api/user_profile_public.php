<?php
require '../connect_db.php';
require '../php-config.php';
session_start();
header('Content-Type: application/json');

if (isset($_SESSION["user_id"])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $user_id = $_GET["user_id"];

        $stmt = $conn->prepare("SELECT fullname, datebirth, address, phone, description FROM User_Profile WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($fullname, $datebirth, $address, $phone, $description);
        

        if ($stmt->fetch()) {
            $user_profile = [
                'status' => "sukses",
                'fullname' => $fullname,
                'birthdate' => $datebirth,
                'address' => $address,
                'phone' => $phone,
                'description' => $description,
            ];

            echo json_encode($user_profile);
            $stmt->close();
            exit;
        }
        echo json_encode([
            "status" => "error",
            "message" => "User tidak Ditemukan"

        ]);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $input = json_decode(file_get_contents('php://input'), true);


        $user_id;
        $fullname = isset($input['fullname']) ? trim($input['nama_lengkap']) : '';
        $datebirth = isset($input['datebirth']) ? trim($input['tgl_lahir']) : null;
        $address = isset($input['address']) ? trim($input['alamat']) : '';
        $phone = isset($input['phone']) ? trim($input['no_hp']) : '';

        try {
            $stmt = $conn->prepare("
            UPDATE User_Profile
            SET fullname = ?, datebirth = ?, address = ?, phone = ?
            WHERE user_id = ?
            ");
            $stmt->bind_param("ssssi", $fullname, $datebirth, $address, $phone, $user_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode([
                        "status" => "sukses",
                        "message" => "Profile berhasil diperbarui"
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Tidak ada perubahan data atau user tidak ditemukan"
                    ]);
                }
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Terjadi kesalahan saat update: " . $conn->error
                ]);
            }

            $stmt->close();

        } catch (mysqli_sql_exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "SQL ERROR: " . $e->getMessage()
            ]);
            exit;
        }
    }
}
echo json_encode([
    "status" => "error",
    "message" => "Anda Tidak Memili Akses"
]);
exit;