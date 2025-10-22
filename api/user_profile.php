<?php

require '../connect_db.php';
require '../php-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //DUMMY
    $user_id = 1;

    $stmt = $conn->prepare("SELECT fullname, datebirth, address, phone FROM UserProfile WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($fullname, $datebirth, $address, $phone);
    $stmt->fetch();


    
    if ($stmt->num_rows > 0) {
        $user_profile = [
            'Nama Lengkap' => $fullname,
            'Tanggal Lahir' => $datebirth,
            'Alamat' => $address,
            'Nomer HP' => $phone,
        ];

        echo json_encode($user_profile);
        $stmt->close();
        exit;
    }
    echo json_encode([
        "status" => "error",
        "message" => "User tidak Ditemukan"

    ]);
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
        UPDATE UserProfile
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
echo json_encode([
    "status" => "error",
    "message" => "Anda Tidak Memili Akses"
]);
exit;