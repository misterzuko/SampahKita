<?php
require '../connect_db.php';
require '../php-config.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {


}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


}

echo json_encode([
    "status" => "error",
    "message" => "Anda Tidak Memili Akses"
]);
exit;