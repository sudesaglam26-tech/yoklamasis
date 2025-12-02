<?php
// ogrenci_kayit.php (Öğrencinin Geçmiş Katılım Kayıtları)
include 'db.php'; 

// 1. Giriş Kontrolü
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Eğer öğrenci zaten giriş yapmadıysa ana sayfaya yönlendir (numara girmesini sağla)
    header("Location: index.php"); 
    exit;
}

$ogr_no = $_SESSION['ogr_no'];
$ad = $_SESSION['ad'];
$kayitlar = [];

// 2. Öğrencinin katıldığı tüm yoklamaları çekme
// INNER JOIN kullanarak katılım tablosu ile yoklamalar tablosunu birleştiriyoruz.
$sql_kayitlar = "
    SELECT 
        y.ders_adi, 
        y.baslangic_zamani, 
        k.zaman_damgasi
    FROM 
        katilimlar k
    INNER JOIN 
        yoklamalar y ON k.yoklama_id = y.yoklama_id
    WHERE 
        k.ogr_no = '$ogr_no'
    ORDER BY 
        y.baslangic_zamani DESC
";

$result_kayitlar = $conn->query($sql_kayitlar);

if ($result_kayitlar->num_rows > 0) {
    while($row = $result_kayitlar->fetch_assoc()) {
        $kayitlar[] = $row;
    }
}
// Başarılı/Başarısız mesajları için genel bir değişken şimdilik kullanmıyoruz.
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Geçmiş Katılım Kayıtları</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f7fa; text-align: center; padding-top: 30px; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); max-width: 800px; margin: auto; }
        h1 { color: #1e8449; }
        .info-header { background-color: #e0f7fa; color: #00838f; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .empty-message { padding: 20px; color: #888; border: 1px dashed #ddd; }
        .nav-link { margin-top: 20px; display: block; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Geçmiş Yoklama Kayıtlarınız</h1>
        
        <div class="info-header">
            Giriş Yapan: **<?php echo htmlspecialchars($ad); ?>** (No: <?php echo $ogr_no; ?>)
            <span class="nav-link"><a href="yoklama.php">⬅️ Aktif Yoklama Ekranına Geri Dön</a> | <a href="logout.php">Çıkış Yap</a></span>
        </div>
        
        <?php if (!empty($kayitlar)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Ders Adı</th>
                        <th>Ders Tarihi ve Saati</th>
                        <th>Katılım Onay Saati</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kayitlar as $kayit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($kayit['ders_adi']); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($kayit['baslangic_zamani'])); ?></td>
                            <td><?php echo date('d.m.Y H:i:s', strtotime($kayit['zaman_damgasi'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-message">
                Henüz katıldığınız bir yoklama kaydı bulunmamaktadır.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>