-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: simpas
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `absensis`
--

DROP TABLE IF EXISTS `absensis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `absensis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `aktivitas` varchar(255) NOT NULL,
  `sub_aktivitas` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `absensis_user_id_foreign` (`user_id`),
  CONSTRAINT `absensis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensis`
--

LOCK TABLES `absensis` WRITE;
/*!40000 ALTER TABLE `absensis` DISABLE KEYS */;
/*!40000 ALTER TABLE `absensis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aktivitas`
--

DROP TABLE IF EXISTS `aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aktivitas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aktivitas`
--

LOCK TABLES `aktivitas` WRITE;
/*!40000 ALTER TABLE `aktivitas` DISABLE KEYS */;
/*!40000 ALTER TABLE `aktivitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aktivitas_kategoris`
--

DROP TABLE IF EXISTS `aktivitas_kategoris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aktivitas_kategoris` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aktivitas_kategoris`
--

LOCK TABLES `aktivitas_kategoris` WRITE;
/*!40000 ALTER TABLE `aktivitas_kategoris` DISABLE KEYS */;
/*!40000 ALTER TABLE `aktivitas_kategoris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deresans`
--

DROP TABLE IF EXISTS `deresans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deresans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `sesi` enum('Bada Subuh','Bada Ashar') NOT NULL,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Udzur','Alfa','Libur') NOT NULL DEFAULT 'Hadir',
  `poin` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deresans_santri_id_tanggal_sesi_unique` (`santri_id`,`tanggal`,`sesi`),
  KEY `deresans_tanggal_sesi_index` (`tanggal`,`sesi`),
  CONSTRAINT `deresans_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deresans`
--

LOCK TABLES `deresans` WRITE;
/*!40000 ALTER TABLE `deresans` DISABLE KEYS */;
INSERT INTO `deresans` VALUES (1,1,'2026-07-29','Bada Subuh','Hadir',2,'2026-07-29 06:02:47','2026-07-29 06:02:47',NULL),(2,2,'2026-09-08','Bada Subuh','Hadir',2,'2026-09-08 08:52:24','2026-09-08 08:52:24',NULL),(3,2,'2026-09-08','Bada Ashar','Alfa',-2,'2026-09-08 08:52:24','2026-09-08 08:52:24',NULL),(4,1,'2026-09-08','Bada Subuh','Hadir',2,'2026-09-08 08:52:25','2026-09-08 08:52:25',NULL),(5,1,'2026-09-08','Bada Ashar','Hadir',2,'2026-09-08 08:52:26','2026-09-08 08:52:26',NULL);
/*!40000 ALTER TABLE `deresans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_absensis`
--

DROP TABLE IF EXISTS `detail_absensis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_absensis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `absensi_id` bigint(20) unsigned NOT NULL,
  `santri_id` bigint(20) unsigned NOT NULL,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Udzur','Alfa','Libur') NOT NULL DEFAULT 'Hadir',
  `poin` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_absensis_absensi_id_foreign` (`absensi_id`),
  KEY `detail_absensis_santri_id_foreign` (`santri_id`),
  CONSTRAINT `detail_absensis_absensi_id_foreign` FOREIGN KEY (`absensi_id`) REFERENCES `absensis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_absensis_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_absensis`
--

LOCK TABLES `detail_absensis` WRITE;
/*!40000 ALTER TABLE `detail_absensis` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_absensis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diniyahs`
--

DROP TABLE IF EXISTS `diniyahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diniyahs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Udzur','Alfa','Libur') NOT NULL,
  `poin` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `diniyahs_santri_id_tanggal_unique` (`santri_id`,`tanggal`),
  CONSTRAINT `diniyahs_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diniyahs`
--

LOCK TABLES `diniyahs` WRITE;
/*!40000 ALTER TABLE `diniyahs` DISABLE KEYS */;
/*!40000 ALTER TABLE `diniyahs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jamaahs`
--

DROP TABLE IF EXISTS `jamaahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jamaahs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` enum('Subuh','Dzuhur','Ashar','Maghrib','Isya') NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Udzur','Terlambat','Alfa','Libur') NOT NULL DEFAULT 'Hadir',
  `poin` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jamaahs_santri_id_tanggal_waktu_unique` (`santri_id`,`tanggal`,`waktu`),
  CONSTRAINT `jamaahs_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jamaahs`
--

LOCK TABLES `jamaahs` WRITE;
/*!40000 ALTER TABLE `jamaahs` DISABLE KEYS */;
INSERT INTO `jamaahs` VALUES (2,2,'2026-07-29','Ashar','Hadir',2,'2026-07-29 06:18:44','2026-09-03 08:19:37','2026-09-03 08:19:37'),(3,1,'2026-07-29','Ashar','Hadir',2,'2026-07-29 06:18:44','2026-07-29 06:18:44',NULL),(4,2,'2026-07-29','Subuh','Hadir',2,'2026-08-14 07:41:48','2026-08-14 07:41:48',NULL),(5,2,'2026-09-08','Subuh','Hadir',2,'2026-09-08 07:03:09','2026-09-09 06:15:57','2026-09-09 06:15:57'),(6,2,'2026-09-08','Dzuhur','Hadir',2,'2026-09-08 07:03:09','2026-09-09 06:16:04','2026-09-09 06:16:04'),(7,2,'2026-09-08','Ashar','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(8,2,'2026-09-08','Maghrib','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(9,2,'2026-09-08','Isya','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(10,1,'2026-09-08','Subuh','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(11,1,'2026-09-08','Dzuhur','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(12,1,'2026-09-08','Ashar','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(13,1,'2026-09-08','Maghrib','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(14,1,'2026-09-08','Isya','Hadir',2,'2026-09-08 07:03:09','2026-09-08 07:03:09',NULL),(15,2,'2026-09-09','Subuh','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(16,2,'2026-09-09','Dzuhur','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(17,2,'2026-09-09','Ashar','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(18,2,'2026-09-09','Maghrib','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(19,2,'2026-09-09','Isya','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(20,1,'2026-09-09','Subuh','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(21,1,'2026-09-09','Dzuhur','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(22,1,'2026-09-09','Ashar','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(23,1,'2026-09-09','Maghrib','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL),(24,1,'2026-09-09','Isya','Hadir',2,'2026-09-09 06:27:37','2026-09-09 06:27:37',NULL);
/*!40000 ALTER TABLE `jamaahs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_20_091451_create_santris_table',1),(5,'2026_06_20_091504_create_aktivitas_kategoris_table',1),(6,'2026_06_20_091519_create_aktivitas_table',1),(7,'2026_06_20_091531_create_prestasis_table',1),(8,'2026_06_20_091539_create_pelanggarans_table',1),(9,'2026_07_29_025750_create_jamaahs_table',1),(10,'2026_07_29_034134_create_absensis_table',1),(11,'2026_07_29_034214_create_detail_absensis_table',1),(12,'2026_07_29_040136_create_pengajians_table',1),(13,'2026_07_29_100423_create_deresans_table',1),(14,'2026_07_30_010709_create_sorogans_table',2),(15,'2026_07_30_013338_create_murojaahs_table',3),(16,'2026_07_30_131317_create_ratibans_table',4),(17,'2026_07_30_131831_create_diniyahs_table',5),(18,'2026_08_09_132726_create_prestasis_table',6),(19,'2026_08_14_120000_add_fields_to_prestasis_table',7),(20,'2026_08_14_131500_add_auto_fields_to_pelanggarans',8),(21,'2026_08_14_140000_add_missing_fields_to_prestasis',8),(22,'2026_09_03_000001_add_soft_deletes_to_report_tables',9),(23,'2026_09_03_000002_repair_pelanggaran_columns',10);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `murojaahs`
--

DROP TABLE IF EXISTS `murojaahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `murojaahs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Udzur','Alfa') NOT NULL,
  `poin` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `murojaahs_santri_id_tanggal_unique` (`santri_id`,`tanggal`),
  CONSTRAINT `murojaahs_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `murojaahs`
--

LOCK TABLES `murojaahs` WRITE;
/*!40000 ALTER TABLE `murojaahs` DISABLE KEYS */;
INSERT INTO `murojaahs` VALUES (1,2,'2026-07-30','Terlambat',1,'2026-07-29 18:39:34','2026-07-29 18:39:34',NULL),(2,1,'2026-07-30','Hadir',2,'2026-07-29 18:39:34','2026-07-29 18:39:34',NULL);
/*!40000 ALTER TABLE `murojaahs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pelanggarans`
--

DROP TABLE IF EXISTS `pelanggarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pelanggarans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sumber` enum('MANUAL','OTOMATIS') NOT NULL DEFAULT 'MANUAL',
  `periode_start` date DEFAULT NULL,
  `periode_end` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `santri_id` bigint(20) unsigned DEFAULT NULL,
  `jenis_pelanggaran` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `tingkat` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `poin` int(11) NOT NULL DEFAULT -1,
  PRIMARY KEY (`id`),
  KEY `pelanggarans_santri_id_foreign` (`santri_id`),
  CONSTRAINT `pelanggarans_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pelanggarans`
--

LOCK TABLES `pelanggarans` WRITE;
/*!40000 ALTER TABLE `pelanggarans` DISABLE KEYS */;
/*!40000 ALTER TABLE `pelanggarans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengajians`
--

DROP TABLE IF EXISTS `pengajians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pengajians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Udzur','Alfa','Libur') NOT NULL DEFAULT 'Hadir',
  `poin` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengajians_santri_id_tanggal_unique` (`santri_id`,`tanggal`),
  CONSTRAINT `pengajians_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengajians`
--

LOCK TABLES `pengajians` WRITE;
/*!40000 ALTER TABLE `pengajians` DISABLE KEYS */;
INSERT INTO `pengajians` VALUES (1,1,'2026-07-29','Hadir',2,'2026-07-29 06:03:40','2026-07-29 06:03:40',NULL),(2,2,'2026-07-29','Hadir',2,'2026-07-29 06:33:33','2026-07-29 06:33:33',NULL),(3,2,'2026-07-28','Alfa',-2,'2026-07-29 06:34:05','2026-07-29 06:34:05',NULL),(4,1,'2026-07-28','Hadir',2,'2026-07-29 06:34:05','2026-07-29 06:34:05',NULL),(5,2,'2026-07-30','Hadir',2,'2026-07-30 06:06:04','2026-07-30 06:06:04',NULL),(6,1,'2026-07-30','Hadir',2,'2026-07-30 06:06:04','2026-07-30 06:06:04',NULL);
/*!40000 ALTER TABLE `pengajians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prestasis`
--

DROP TABLE IF EXISTS `prestasis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestasis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tingkat` varchar(255) DEFAULT NULL,
  `peringkat` varchar(255) DEFAULT NULL,
  `poin` int(11) NOT NULL DEFAULT 0,
  `tahun` smallint(6) DEFAULT NULL,
  `santri_id` bigint(20) unsigned DEFAULT NULL,
  `nama_prestasi` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prestasis_santri_id_foreign` (`santri_id`),
  CONSTRAINT `prestasis_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestasis`
--

LOCK TABLES `prestasis` WRITE;
/*!40000 ALTER TABLE `prestasis` DISABLE KEYS */;
/*!40000 ALTER TABLE `prestasis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratibans`
--

DROP TABLE IF EXISTS `ratibans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratibans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Udzur','Alfa','Libur') NOT NULL,
  `poin` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ratibans_santri_id_tanggal_unique` (`santri_id`,`tanggal`),
  CONSTRAINT `ratibans_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratibans`
--

LOCK TABLES `ratibans` WRITE;
/*!40000 ALTER TABLE `ratibans` DISABLE KEYS */;
INSERT INTO `ratibans` VALUES (1,2,'2026-07-30','Hadir',2,'2026-07-30 06:17:33','2026-07-30 06:17:33',NULL),(2,1,'2026-07-30','Hadir',2,'2026-07-30 06:17:33','2026-07-30 06:17:33',NULL);
/*!40000 ALTER TABLE `ratibans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `santris`
--

DROP TABLE IF EXISTS `santris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `santris` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_kelamin` enum('PUTRA','PUTRI') NOT NULL,
  `alamat` text NOT NULL,
  `kamar` varchar(255) NOT NULL,
  `nama_ayah` varchar(255) NOT NULL,
  `pekerjaan_ayah` varchar(255) DEFAULT NULL,
  `nama_ibu` varchar(255) NOT NULL,
  `pekerjaan_ibu` varchar(255) DEFAULT NULL,
  `nomor_hp_orangtua` varchar(255) NOT NULL,
  `status_diniah` enum('1 WUSTHO','2 WUSTHO','3 WUSTHO','1 ULYA','2 ULYA','3 ULYA','ASATIDZAH','MUTIMIN') NOT NULL,
  `status_formal` enum('PELAJAR','MAHASISWA','TIDAK SEKOLAH') NOT NULL,
  `arsip_hataman` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status_aktif` enum('AKTIF','ALUMNI','PINDAH','KELUAR') NOT NULL DEFAULT 'AKTIF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `santris_nis_unique` (`nis`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `santris`
--

LOCK TABLES `santris` WRITE;
/*!40000 ALTER TABLE `santris` DISABLE KEYS */;
INSERT INTO `santris` VALUES (1,'RQOO1','NAELI RAKHMAWATI','PUTRI','SUMINGKIR 02/04,JERUKLEGI, CILACAP','06','RASIWAN','WIRAUSAHA','NUR HAENI','ART','08816715599','ASATIDZAH','MAHASISWA','NANDBG','santri/foto/TsdiLFLgvvKlUn3RscXtki4P3mD0nomOhxTUDY4Y.png','AKTIF','2026-07-29 06:01:59','2026-07-29 06:01:59'),(2,'RQ002','MAILATUSSAFAAH','PUTRI','KEMBARAN, BANYUMAS','06','RAHMAT','GURU','NUR','ART','087654566657','1 ULYA','MAHASISWA',NULL,'santri/foto/SPG1BRaW4s5zw7CjPNBgb3avlmUiiIHWFseLNKbQ.png','AKTIF','2026-07-29 06:18:27','2026-07-29 06:18:27');
/*!40000 ALTER TABLE `santris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sorogans`
--

DROP TABLE IF EXISTS `sorogans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sorogans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Terlambat','Izin','Sakit','Udzur','Alfa','Libur') NOT NULL DEFAULT 'Hadir',
  `poin` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sorogans_santri_id_tanggal_unique` (`santri_id`,`tanggal`),
  CONSTRAINT `sorogans_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sorogans`
--

LOCK TABLES `sorogans` WRITE;
/*!40000 ALTER TABLE `sorogans` DISABLE KEYS */;
INSERT INTO `sorogans` VALUES (1,2,'2026-07-30','Hadir',2,'2026-07-29 18:18:18','2026-07-29 18:18:18',NULL),(2,1,'2026-07-30','Hadir',2,'2026-07-29 18:18:18','2026-07-29 18:18:18',NULL);
/*!40000 ALTER TABLE `sorogans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin',NULL,NULL,'$2y$12$9xldqIzGUDMo4t2U0EEJ5.lfLYVxsqhZj2BZqbI1.MX0Sjs/Fx0za',NULL,'2026-07-29 04:09:19','2026-07-29 04:09:19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-22 22:03:10
