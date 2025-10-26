<?php
//KONFIGURASI SERVER
require 'php-config.php';

$host = "srv1153.hstgr.io";
$user = "u634593617_sampahkita";
$pass = "Sampahkita123";
$db = "u634593617_sampahkita";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>