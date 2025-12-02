<?php
// index.php (GÜNCELLENMİŞ: Aktif Ders Bilgisi ve Sadece Numara Girişi)
include 'db.php'; 

$hata_mesaji = "";
$aktif_ders = null;
$yoklama_aktif = false;

// Aktif Yoklamayı Kontrol Etme
$sql_aktif_yoklama = "SELECT * FROM yoklamalar ORDER BY yoklama_id DESC LIMIT 1";
$result_aktif_yoklama = $conn->query($sql_aktif_yoklama);

if ($result_aktif_yoklama->num_rows > 0) {
    $yoklama = $result_aktif_yoklama->fetch_assoc();
    
    // Yoklama bitiş zamanını hesaplama ve kontrol
    $baslangic = new DateTime($yoklama['baslangic_zamani']);
    $bitis = $baslangic->modify('+' . $yoklama['sure_dk'] . ' minutes');
    $anlik_zaman = new DateTime();

    if ($anlik_zaman < $bitis) {
        $yoklama_aktif = true;
        $aktif_ders = $yoklama['ders_adi'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $yoklama_aktif) {
    $ogr_no = $conn->real_escape_string($_POST['ogr_no']);

    // Öğrenci numarasının geçerliliğini kontrol et
    $sql = "SELECT ogr_no, ad FROM ogrenciler WHERE ogr_no = '$ogr_no'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Session'a sadece öğrenci numarasını ve adını kaydet
        $_SESSION['loggedin'] = true;
        $_SESSION['ogr_no'] = $row['ogr_no'];
        $_SESSION['ad'] = $row['ad'];
        
        // Yoklama sayfasına yönlendir (Burada kontrol edilecek)
        header("Location: yoklama.php"); 
        exit;
    } else {
        $hata_mesaji = "Hatalı Öğrenci Numarası! Lütfen numaranızı kontrol edin.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !$yoklama_aktif) {
     $hata_mesaji = "❌ Şu anda aktif bir yoklama bulunmamaktadır.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yoklama Sistemi Ana Ekran</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e8f5e9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); max-width: 500px; text-align: center; }
        h1 { color: #2e7d32; margin-bottom: 20px; }
        .ders-info { font-size: 1.5em; font-weight: bold; color: #1b5e20; padding: 10px; border: 2px solid #a5d6a7; border-radius: 8px; margin-bottom: 30px; }
        input[type="text"] { width: 80%; padding: 12px; margin: 15px 0; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 1.2em; text-align: center; }
        button { background-color: #4CAF50; color: white; padding: 15px 30px; border: none; border-radius: 6px; cursor: pointer; width: 80%; font-size: 1.2em; }
        .error { color: red; text-align: center; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Yoklama Sistemi</h1>
        
        <?php if ($aktif_ders): ?>
            <div class="ders-info">
                Aktif Ders: <?php echo htmlspecialchars($aktif_ders); ?>
            </div>

            <form action="index.php" method="post">
                <label for="ogr_no">Lütfen Öğrenci Numaranızı Girin:</label>
                <input type="text" id="ogr_no" name="ogr_no" required>

                <button type="submit">Listeyi Aç</button>
            </form>
            
        <?php else: ?>
            <div class="ders-info" style="color: #d32f2f; border-color: #ffcdd2;">
                ❌ Şu an aktif bir yoklama bulunmamaktadır.
            </div>
        <?php endif; ?>

        <?php if ($hata_mesaji): ?>
            <p class="error"><?php echo $hata_mesaji; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>