<?php
// db.php

// !!! KRİTİK DÜZELTME: Session'ı en başta başlatıyoruz
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Europe/Istanbul');


$servername = "localhost";
$username = "root"; // XAMPP varsayılan kullanıcı adı
$password = ""; // XAMPP varsayılan şifre (Boş)
$dbname = "yoklama_sistemi"; // Sizin veritabanı adınız

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// Karakter setini ayarlama (Türkçe karakterler için)
$conn->set_charset("utf8");

// NOT: Bu dosyada başka hiçbir kod veya boşluk OLMAMALIDIR!
?>