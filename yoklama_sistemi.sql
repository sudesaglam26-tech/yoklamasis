-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 02 Ara 2025, 14:56:48
-- Sunucu sürümü: 10.4.28-MariaDB
-- PHP Sürümü: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `yoklama_sistemi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `katilimlar`
--

CREATE TABLE `katilimlar` (
  `katilim_id` int(11) NOT NULL,
  `yoklama_id` int(11) NOT NULL,
  `ogr_no` int(11) NOT NULL,
  `zaman_damgasi` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `katilimlar`
--

INSERT INTO `katilimlar` (`katilim_id`, `yoklama_id`, `ogr_no`, `zaman_damgasi`) VALUES
(1, 1, 101, '2025-12-01 15:33:03'),
(2, 2, 101, '2025-12-01 15:42:38'),
(3, 3, 101, '2025-12-01 15:45:51'),
(4, 3, 231203037, '2025-12-01 15:49:27'),
(5, 4, 101, '2025-12-01 15:51:23'),
(6, 4, 231203037, '2025-12-01 15:51:43'),
(7, 6, 231203037, '2025-12-01 16:40:10');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenciler`
--

CREATE TABLE `ogrenciler` (
  `ogr_no` int(11) NOT NULL,
  `ad` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogrenciler`
--

INSERT INTO `ogrenciler` (`ogr_no`, `ad`, `sifre`) VALUES
(101, 'Ahmet Yılmaz', '202cb962ac59075b964b07152d234b70'),
(231203001, 'Ayşe Dürüst', 'tyu25485bsjdie48plkhg'),
(231203006, 'Muhammet Kardemir', 'werxdr148lkrt'),
(231203011, 'Mustafa Aslan', 'bygre67kocdes5479123'),
(231203015, 'Zeynep Kaygusuz', 'bsdb1556sjadund23256f'),
(231203024, 'Merve Şahin', 'dbjdw6872169'),
(231203027, 'Aleyna Akıllanmaz', 'rtydsa89njvd3247'),
(231203032, 'Ahmet Durmaz', 'dhwnk4565dwdht269'),
(231203037, 'Mina Sağlam', '20153037'),
(231203039, 'Mert Bozan', 'dnej54985dwbuxg26'),
(231203045, 'Şerife Demir', 'asd852klm97ert21'),
(231203054, 'Adil Koçari', '1566bh34895bdtr15n24897');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogretmenler`
--

CREATE TABLE `ogretmenler` (
  `ogretmen_id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `ad_soyad` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogretmenler`
--

INSERT INTO `ogretmenler` (`ogretmen_id`, `kullanici_adi`, `sifre`, `ad_soyad`) VALUES
(1, 'ogretmen', '250cf8b51c773f3f8dc8b4be867a9a02', 'Doç. Dr. Ahmet Saruhan');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yoklamalar`
--

CREATE TABLE `yoklamalar` (
  `yoklama_id` int(11) NOT NULL,
  `ders_adi` varchar(100) NOT NULL,
  `baslangic_zamani` datetime NOT NULL,
  `sure_dk` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `yoklamalar`
--

INSERT INTO `yoklamalar` (`yoklama_id`, `ders_adi`, `baslangic_zamani`, `sure_dk`) VALUES
(1, 'Sistem Tasarımı', '2025-12-01 15:32:20', 5),
(2, 'Sistem Tasarımı', '2025-12-01 15:42:18', 2),
(3, 'Sistem Tasarımı', '2025-12-01 15:45:43', 5),
(4, 'Sistem Tasarımı', '2025-12-01 15:51:09', 1),
(5, 'Sistem Tasarımı2', '2025-12-01 15:57:17', 5),
(6, 'Sistem Tasarımı', '2025-12-01 18:31:00', 39),
(7, 'Sistem Tasarımı', '2025-12-01 19:57:00', 0),
(8, 'Sistem Tasarımı', '2025-12-01 19:57:00', 1),
(9, 'Sistem Tasarımı', '2025-12-01 19:57:00', 1),
(10, 'Sistem Tasarımı', '2025-12-01 19:58:00', 0),
(11, 'Sistem Tasarımı', '2025-12-01 19:58:00', 0),
(12, 'Sistem Tasarımı', '2025-12-01 19:58:00', 0),
(13, 'Sistem Tasarımı', '2025-12-01 19:58:00', 1),
(14, 'Sistem Tasarımı', '2025-12-01 19:59:00', 0),
(15, 'sistem tasarımı', '2025-12-01 20:17:00', 1),
(16, 'sistem tasarımı', '2025-12-01 20:18:00', 0),
(17, 'sistem tasarımı', '2025-12-02 00:05:00', 0),
(18, 'sistem tasarımı3', '2025-12-02 00:16:00', 0),
(19, 'sistem tasarımı', '2025-12-02 00:17:00', 1),
(20, 'sistem tasarımı', '2025-12-02 00:24:00', 0),
(21, 'sistem tasarımı', '2025-12-02 00:24:00', 1),
(22, 'sistem tasarımı', '2025-12-02 00:25:00', 0),
(23, 'sistem tasarımı4', '2025-12-02 00:33:00', 1),
(24, 'WEB', '2025-12-02 00:41:00', 0),
(25, 'web1', '2025-12-02 00:44:00', 1),
(26, 'web4', '2025-12-02 15:08:00', 0),
(27, 'ağlar1', '2025-12-02 15:36:00', 0),
(28, 'veri1', '2025-12-02 15:46:00', 0),
(29, 'veri1', '2025-12-02 15:46:00', 1),
(30, 'fizik1', '2025-12-02 15:54:00', 0),
(31, 'veri2', '2025-12-02 16:00:00', 1),
(32, 'sistem tasarımı', '2025-12-02 16:33:00', 0);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `katilimlar`
--
ALTER TABLE `katilimlar`
  ADD PRIMARY KEY (`katilim_id`),
  ADD KEY `yoklama_id` (`yoklama_id`),
  ADD KEY `ogr_no` (`ogr_no`);

--
-- Tablo için indeksler `ogrenciler`
--
ALTER TABLE `ogrenciler`
  ADD PRIMARY KEY (`ogr_no`);

--
-- Tablo için indeksler `ogretmenler`
--
ALTER TABLE `ogretmenler`
  ADD PRIMARY KEY (`ogretmen_id`),
  ADD UNIQUE KEY `kullanici_adi` (`kullanici_adi`);

--
-- Tablo için indeksler `yoklamalar`
--
ALTER TABLE `yoklamalar`
  ADD PRIMARY KEY (`yoklama_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `katilimlar`
--
ALTER TABLE `katilimlar`
  MODIFY `katilim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `ogretmenler`
--
ALTER TABLE `ogretmenler`
  MODIFY `ogretmen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `yoklamalar`
--
ALTER TABLE `yoklamalar`
  MODIFY `yoklama_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `katilimlar`
--
ALTER TABLE `katilimlar`
  ADD CONSTRAINT `katilimlar_ibfk_1` FOREIGN KEY (`yoklama_id`) REFERENCES `yoklamalar` (`yoklama_id`),
  ADD CONSTRAINT `katilimlar_ibfk_2` FOREIGN KEY (`ogr_no`) REFERENCES `ogrenciler` (`ogr_no`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
