<?php
$host = "https://auth-db1153.hstgr.io/";
$user = "sampahkita";   
$pass = "Sampahkita123_";
$db   = "u634593617_sampahkita"; 


$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
echo "Koneksi berhasil!";
?>
