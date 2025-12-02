<?php
// yoklama.php (GÜNCEL: Sınıf Listesi ve Tek Hamle Onayı)
include 'db.php'; 

// 1. Giriş Kontrolü (Numara girişi yapıldı mı?)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$ogr_no = $_SESSION['ogr_no'];
$ad = $_SESSION['ad'];
$mesaj = "";
$yoklama_aktif = false;
$aktif_yoklama_id = 0;
$ogrenci_katilim_durumu = false;
$kalan_sure_saniye = 0;

// 2. Aktif Yoklamayı Kontrol Etme
$sql_aktif_yoklama = "SELECT * FROM yoklamalar ORDER BY yoklama_id DESC LIMIT 1";
$result_aktif_yoklama = $conn->query($sql_aktif_yoklama);

if ($result_aktif_yoklama->num_rows > 0) {
    $yoklama = $result_aktif_yoklama->fetch_assoc();
    $aktif_yoklama_id = $yoklama['yoklama_id'];
    $aktif_ders = $yoklama['ders_adi'];
    
    // Süre kontrolü
    $baslangic = new DateTime($yoklama['baslangic_zamani']);
    $bitis = $baslangic->modify('+' . $yoklama['sure_dk'] . ' minutes');
    $anlik_zaman = new DateTime();

    if ($anlik_zaman < $bitis) {
        $yoklama_aktif = true;
        $aralik = $anlik_zaman->diff($bitis);
        $kalan_sure_saniye = $aralik->s + $aralik->i * 60 + $aralik->h * 3600; 
        
        // 3. Öğrencinin Daha Önce Katılıp Katılmadığını Kontrol Etme
        $sql_katilim = "SELECT * FROM katilimlar WHERE yoklama_id = '$aktif_yoklama_id' AND ogr_no = '$ogr_no'";
        $result_katilim = $conn->query($sql_katilim);
        
        if ($result_katilim->num_rows > 0) {
            $ogrenci_katilim_durumu = true;
            $mesaj = "✅ Katılımınız kaydedildi. İyi dersler ";
        }

    } else {
        // Yoklama süresi dolduysa session'ı temizle
        session_destroy();
        header("Location: index.php");
        exit;
    }
} else {
    // Aktif yoklama yoksa session'ı temizle
    session_destroy();
    header("Location: index.php");
    exit;
}

// 4. Yoklama Kaydetme İşlemi (POST isteği geldiğinde)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['katil']) && $yoklama_aktif && !$ogrenci_katilim_durumu) {
    
    $zaman_damgasi = date('Y-m-d H:i:s');
    
    // Sadece SESSION'daki öğrencinin katılımını kaydet
    $sql_insert = "INSERT INTO katilimlar (yoklama_id, ogr_no, zaman_damgasi) VALUES ('$aktif_yoklama_id', '$ogr_no', '$zaman_damgasi')";
    
    if ($conn->query($sql_insert) === TRUE) {
        // İşlem başarılı, sayfayı yenileyerek durumu güncelle ve butonu kaldır
        header("Location: yoklama.php"); 
        exit;
    } else {
        $mesaj = "Hata oluştu: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yoklama Kayıt Ekranı</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f3f3; text-align: center; padding-top: 30px; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); max-width: 650px; margin: auto; }
        h1 { color: #1e8449; }
        .info-header { background-color: #d1e7dd; color: #0a3622; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
        #timer { font-size: 1.5em; font-weight: bold; color: #c0392b; margin-top: 10px; }
        
        .onay-listesi { border: 1px solid #ccc; border-radius: 8px; overflow: hidden; margin-top: 20px; }
        .onay-listesi h3 { background-color: #2ecc71; color: white; padding: 12px; margin: 0; }
        .ogrenci-satir { padding: 12px 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .ogrenci-satir:last-child { border-bottom: none; }
        .ogrenci-satir-self { background-color: #f0fff0; font-weight: bold; }
        
        button { background-color: #27ae60; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .katildi-mesaj { color: green; font-weight: bold; }
        .hata-mesaji { color: red; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Yoklama Kayıt Sayfası</h1>
        <div class="info-header">
            Ders: <?php echo htmlspecialchars($aktif_ders); ?>
            <div id="timer">Kalan Süre: <span id="saniye"><?php echo $kalan_sure_saniye; ?></span> saniye</div>
            <p style="margin: 5px 0 0 0;">
                Giriş Yapan: <?php echo htmlspecialchars($ad); ?>
                 (No: <?php echo $ogr_no; ?>) 
                 | <a href="ogrenci_kayit.php">📚 Geçmiş Kayıtları Gör</a>
                 | <a href="logout.php">Çıkış Yap</a></p>
        </div>
        
        <div class="onay-listesi">
            <h3>Sınıf Listesi</h3>
            
            <?php
            // Tüm öğrencileri listele (sınıfın tamamı)
            $sql_list = "SELECT ogr_no, ad FROM ogrenciler ORDER BY ogr_no ASC";
            $result_list = $conn->query($sql_list);
            
            if ($result_list->num_rows > 0) {
                while($row = $result_list->fetch_assoc()): ?>
                    <div class="ogrenci-satir <?php echo ($row['ogr_no'] == $ogr_no) ? 'ogrenci-satir-self' : ''; ?>">
                        <span><?php echo $row['ogr_no'] . ' - ' . htmlspecialchars($row['ad']); ?></span>
                        
                        <?php if ($row['ogr_no'] == $ogr_no): ?>
                            <?php if (!$ogrenci_katilim_durumu): ?>
                                <form method="POST" action="yoklama.php" style="display:inline;">
                                    <button type="submit" name="katil"> ✅ ONAYLA</button>
                                </form>
                            <?php else: ?>
                                <span class="katildi-mesaj">✔️ KATILIM KAYDEDİLDİ</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span></span> 
                        <?php endif; ?>
                    </div>
                <?php endwhile;
            }
            ?>
        </div>
        
        <?php if ($mesaj): ?>
            <p class="hata-mesaji"><?php echo $mesaj; ?></p>
        <?php endif; ?>
    </div>

    <script>
        var saniye = <?php echo $kalan_sure_saniye; ?>;
        var timerElement = document.getElementById('saniye');

        if(saniye > 0) {
            var interval = setInterval(function() {
                saniye--;
                if (timerElement) {
                    timerElement.textContent = saniye;
                }
                
                if (saniye <= 0) {
                    clearInterval(interval);
                    alert("Yoklama süresi doldu! Ana sayfaya yönlendiriliyorsunuz...");
                    window.location.href = 'logout.php'; // Süre bitince oturumu kapatıp ana ekrana yönlendir
                }
            }, 1000);
        }
    </script>
</body>
</html>
</body>
</html>