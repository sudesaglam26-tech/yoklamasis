<?php
session_start();

include 'db.php'; 

// Yönlendirmeden gelen kapatma mesajını al
$mesaj = "";
if (isset($_SESSION['kapatma_mesaji'])) {
    $mesaj = $_SESSION['kapatma_mesaji'];
    unset($_SESSION['kapatma_mesaji']); 
} elseif (isset($_SESSION['baslatma_mesaji'])) { // <<< BURASI YENİ
    $mesaj = $_SESSION['baslatma_mesaji'];
    unset($_SESSION['baslatma_mesaji']);
}

// Öğretmen Giriş Kontrolü
if (!isset($_SESSION['ogretmen_loggedin']) || $_SESSION['ogretmen_loggedin'] !== true) {
    header("Location: ogretmen_giris.php");
    exit;
}

$ogretmen_ad = $_SESSION['ogretmen_ad'];
$aktif_ders = null;
$yoklama_basladi = false;
$aktif_yoklama_id = 0;

// 1. Yoklama Başlatma İşlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['baslat'])) {
    
    $ders_adi = $conn->real_escape_string($_POST['ders_adi']);
    $sure_dk_girdi = (int)$_POST['sure_dk']; 

    $secilen_tarih = $conn->real_escape_string($_POST['tarih']); 
    $secilen_saat = $conn->real_escape_string($_POST['saat']);
    
    $baslangic_zamani = $secilen_tarih . ' ' . $secilen_saat . ':00';
    
    $yazilacak_sure = $sure_dk_girdi; 

   
    // Eğer değişkenin değeri (örneğin 5), 1'e eşitlenmeye çalışılırsa, bunu burada engelleyelim
    if ($yazilacak_sure < 1) {
        $yazilacak_sure = 1; // En az 1 dakika olsun
    }
  

    $sql_insert = "INSERT INTO yoklamalar (ders_adi, baslangic_zamani, sure_dk) 
                   VALUES ('$ders_adi', '$baslangic_zamani', $yazilacak_sure)"; 
    





    // 🛑 SON BİR DEFA KAYIT ÖNCESİ KONTROL 🛑
    echo "<h1>KRİTİK TEST: Veritabanına Yazılacak Değer: " . $yazilacak_sure . "</h1>";
    
    // Kayıt işlemini 10 saniye geciktirerek veritabanını kontrol etmenize olanak tanır.
    // Lütfen bu 10 saniye içinde phpMyAdmin'i kontrol edin.
    sleep(10); 






    if ($conn->query($sql_insert) === TRUE) {
        
        $_SESSION['baslatma_mesaji'] = "✅ Yoklama başarıyla başlatıldı!";
        header("Location: ogretmen.php");
        exit;
        
    } else {
        $mesaj = "Veritabanı Kayıt Hatası! Detay: " . $conn->error;
    }
}

// 2. Yoklama Kapatma İşlemi 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kapat']) && isset($_POST['aktif_id'])) {
    $kapatilacak_id = (int)$_POST['aktif_id'];
    
    $sql_get_time = "SELECT baslangic_zamani FROM yoklamalar WHERE yoklama_id = '$kapatilacak_id'";
    $result_time = $conn->query($sql_get_time);
    
    if ($result_time->num_rows > 0) {
        $row_time = $result_time->fetch_assoc();
        $baslangic = new DateTime($row_time['baslangic_zamani']);
        $anlik_zaman = new DateTime();
        
        $fark = $baslangic->diff($anlik_zaman);
        // Geçen süreyi (dakika olarak) hesapla
        $gecen_dakika = $fark->i + ($fark->h * 60) + ($fark->days * 24 * 60); 
        
        $sql_update = "UPDATE yoklamalar SET sure_dk = '$gecen_dakika' WHERE yoklama_id = '$kapatilacak_id'";
        
        if ($conn->query($sql_update) === TRUE) {
            
            // 🛑 GÖRSEL DÜZELTME: İşlem başarılı olunca sayfayı yeniden yükle
            $_SESSION['kapatma_mesaji'] = "🛑 Yoklama başarıyla sonlandırıldı!";
            header("Location: ogretmen.php");
            exit;
            
        } else {
            $mesaj = "Kapatma hatası: " . $conn->error;
        }
    } else {
        $mesaj = "Kapatılacak aktif yoklama bulunamadı.";
    }
}


// 3. Aktif Yoklamayı Kontrol Etme
$sql_aktif = "SELECT * FROM yoklamalar ORDER BY yoklama_id DESC LIMIT 1";
$result_aktif = $conn->query($sql_aktif);

if ($result_aktif->num_rows > 0) {
    $yoklama = $result_aktif->fetch_assoc();
    $aktif_ders = $yoklama['ders_adi'];
    $aktif_yoklama_id = $yoklama['yoklama_id'];
    
    // Süre kontrolü
    $baslangic = new DateTime($yoklama['baslangic_zamani']);
    $bitis = $baslangic->modify('+' . $yoklama['sure_dk'] . ' minutes');
    $anlik_zaman = new DateTime();
    
    if ($anlik_zaman < $bitis) {
        $yoklama_basladi = true;
        $aralik = $anlik_zaman->diff($bitis);
        // Kalan saniyeyi hesapla
        $kalan_saniye = $aralik->s + $aralik->i * 60 + $aralik->h * 3600 + $aralik->days * 24 * 3600;
    } else {
        $yoklama_basladi = false;
        $mesaj = "❌ Son yoklamanın süresi dolmuştur: $aktif_ders";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğretmen Paneli</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f8ff; text-align: center; padding: 20px; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); max-width: 700px; margin: auto; }
        h1 { color: #1565c0; }
        .info-box { margin-bottom: 20px; padding: 15px; border-radius: 5px; background-color: #e3f2fd; border-left: 5px solid #1565c0; }
        input[type="text"], input[type="number"], input[type="date"], input[type="time"] { 
            padding: 10px; margin: 5px; border: 1px solid #ccc; border-radius: 4px; display: inline-block; width: auto; 
        }
        .form-group { margin-bottom: 15px; }
        .btn-kapat { background-color: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 15px; font-weight: bold; color: #00897b; }
        #timer-kalan { font-size: 1.2em; color: #d32f2f; }
        .aktif-ders { font-size: 1.5em; font-weight: bold; color: #1565c0; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Öğretmen Paneli - <?php echo htmlspecialchars($ogretmen_ad); ?></h1>
        <div class="info-box">
            <a href="logout.php">Çıkış Yap</a>
        </div>
        
        <?php if ($mesaj): ?>
            <p class="message"><?php echo $mesaj; ?></p>
        <?php endif; ?>

        <?php if ($yoklama_basladi): ?>
            <div class="aktif-ders">
                AKTİF YOKLAMA: <?php echo htmlspecialchars($aktif_ders); ?>
            </div>







            <?php 
               if ($yoklama_basladi) {
                   echo "<div style='color: blue; font-size: 20px;'>PHP Tarafından Hesaplanan Değer: " . $kalan_saniye . " saniye</div>";
               }
            ?>








            <p>Kalan Süre: <span id="timer-kalan"><?php echo $kalan_saniye; ?></span> saniye</p>
            
            <form method="POST" action="ogretmen.php" onsubmit="return confirm('Aktif yoklamayı sonlandırmak istediğinizden emin misiniz?');">
                <input type="hidden" name="aktif_id" value="<?php echo $aktif_yoklama_id; ?>">
                <button type="submit" name="kapat" class="btn-kapat">🛑 Yoklamayı Şimdi Kapat</button>
            </form>
            <hr>
            <p>Yeni bir yoklama başlatmak için lütfen mevcut olanın bitmesini/kapatılmasını bekleyin.</p>
        <?php else: ?>
            <h2>Yeni Yoklama Başlat</h2>
            <form method="POST" action="ogretmen.php">
                <div class="form-group">
                    <label for="ders_adi">Ders Adı:</label>
                    <input type="text" id="ders_adi" name="ders_adi" required placeholder="Örn: Web Programlama">
                </div>
                
                <div class="form-group">
                    <label for="tarih">Başlangıç Tarihi:</label>
                    <input type="date" id="tarih" name="tarih" required value="<?php echo date('Y-m-d'); ?>"> 
                </div>

                <div class="form-group">
                    <label for="saat">Başlangıç Saati:</label>
                    <input type="time" id="saat" name="saat" required value="<?php echo date('H:i'); ?>"> 
                </div>

                <div class="form-group">
                    <label for="sure_dk">Süre (Dakika):</label>
                    <input type="number" id="sure_dk" name="sure_dk" required min="1" max="60" value="5">
                </div>
                
                <button type="submit" name="baslat">Yoklamayı Başlat</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($yoklama_basladi): ?>
    <script>
        // 1. Sayaç değişkenini tanımla
        var saniye = <?php echo $kalan_saniye; ?>;
        var timerElement = document.getElementById('timer-kalan');
        var interval; // interval değişkenini tanımlıyoruz
        
        // Önceki sayaç varsa temizle (Tarayıcı önbelleklemesine karşı önlem)
        if (typeof interval !== 'undefined') {
            clearInterval(interval);
        }
        
        // 2. Sayacı başlat
        interval = setInterval(function() {
            saniye--;
            
            if (saniye <= 0) {
                clearInterval(interval);
                alert("Yoklama süresi doldu! Yeni yoklama başlatabilirsiniz.");
                window.location.reload(); 
            }
            
            if (saniye >= 0) {
               if (timerElement) {
                   timerElement.textContent = saniye;
               }
            } else {
               // Süre negatife düşerse, yine de sayfayı yenile
               clearInterval(interval);
               window.location.reload();
            }
            
        }, 1000);
    </script>
    <?php endif; ?>
</body>
</html>