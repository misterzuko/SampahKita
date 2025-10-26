<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$targetDir = __DIR__ . "/../public/uploads/"; 


if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES["image"])) {
    $fileName = basename($_FILES["image"]["name"]);
    $newName = uniqid("IMG_") . "_" . $fileName;
    $targetFile = $targetDir . $newName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        echo json_encode([
            "success" => true,
            "message" => "Upload berhasil!",
            "url" => "/uploads/" . $newName
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Gagal memindahkan file."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Tidak ada file dikirim."
    ]);
}
echo json_encode([
    "status" => "error",
    "message" => "Anda tidak memiliki akses"
]);
exit;

