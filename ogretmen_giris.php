<?php
session_start();
// ogretmen_giris.php
include 'db.php'; 

$hata_mesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = $conn->real_escape_string($_POST['kullanici_adi']);
    $sifre = $conn->real_escape_string($_POST['sifre']);

    // Şifreyi MD5 ile kontrol et
    $hashed_sifre = md5($sifre); 

    $sql = "SELECT ogretmen_id, ad_soyad FROM ogretmenler WHERE kullanici_adi = '$kullanici_adi' AND sifre = '$hashed_sifre'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        $_SESSION['ogretmen_loggedin'] = true;
        $_SESSION['ogretmen_id'] = $row['ogretmen_id'];
        $_SESSION['ogretmen_ad'] = $row['ad_soyad'];
        
        header("Location: ogretmen.php");
        exit;
    } else {
        $hata_mesaji = "Hatalı Kullanıcı Adı veya Şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğretmen Girişi</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e3f2fd; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); max-width: 400px; }
        h2 { text-align: center; color: #1565c0; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #1565c0; color: white; padding: 14px 20px; margin: 8px 0; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>👨‍🏫 Öğretmen Girişi</h2>
        <?php if ($hata_mesaji): ?>
            <p class="error"><?php echo $hata_mesaji; ?></p>
        <?php endif; ?>
        <form action="ogretmen_giris.php" method="post">
            <label for="kullanici_adi">Kullanıcı Adı:</label>
            <input type="text" id="kullanici_adi" name="kullanici_adi" required>

            <label for="sifre">Şifre:</label>
            <input type="password" id="sifre" name="sifre" required>

            <button type="submit">Giriş Yap</button>
        </form>
    </div>
</body>
</html>