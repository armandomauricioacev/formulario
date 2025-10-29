CREATE DATABASE  IF NOT EXISTS `solicitud_servicios` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `solicitud_servicios`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: solicitud_servicios
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `coordinaciones`
--

DROP TABLE IF EXISTS `coordinaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coordinaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de coordinaciones del IMT';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coordinaciones`
--

LOCK TABLES `coordinaciones` WRITE;
/*!40000 ALTER TABLE `coordinaciones` DISABLE KEYS */;
INSERT INTO `coordinaciones` VALUES (1,'Coordinación de Desarrollo','Responsable del desarrollo de proyectos, innovación y nuevas tecnologías',1,'2025-10-29 22:27:16'),(2,'Coordinación de Infraestructura','Encargada del mantenimiento, calibración y gestión de infraestructura',1,'2025-10-29 22:27:16'),(3,'Coordinación de Soporte','Brinda soporte técnico, capacitación y asistencia a usuarios',1,'2025-10-29 22:27:16'),(4,'Coordinación de Telemática','Gestiona sistemas telemáticos, comunicaciones y tecnologías de información',1,'2025-10-29 22:27:16');
/*!40000 ALTER TABLE `coordinaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entidades_procedencia`
--

DROP TABLE IF EXISTS `entidades_procedencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entidades_procedencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de entidades de procedencia';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entidades_procedencia`
--

LOCK TABLES `entidades_procedencia` WRITE;
/*!40000 ALTER TABLE `entidades_procedencia` DISABLE KEYS */;
INSERT INTO `entidades_procedencia` VALUES (1,'Universidad Politécnica de Querétaro',1,'2025-10-29 22:27:16'),(2,'Centro de Investigación y Desarrollo Tecnológico',1,'2025-10-29 22:27:16'),(3,'Instituto Tecnológico de Estudios Superiores',1,'2025-10-29 22:27:16');
/*!40000 ALTER TABLE `entidades_procedencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `servicios`
--

DROP TABLE IF EXISTS `servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coordinacion_predeterminada_id` int NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_coordinacion` (`coordinacion_predeterminada_id`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`coordinacion_predeterminada_id`) REFERENCES `coordinaciones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de servicios con coordinación predeterminada';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,'Calibración de Equipos',2,'Servicio de calibración de equipos de medición y sensores',1,'2025-10-29 22:27:16'),(2,'Mantenimiento de Infraestructura',2,'Mantenimiento preventivo y correctivo de infraestructura física',1,'2025-10-29 22:27:16'),(3,'Certificación de Equipos',2,'Certificación y validación de equipos de laboratorio',1,'2025-10-29 22:27:16'),(4,'Desarrollo de Software',1,'Desarrollo de aplicaciones y sistemas personalizados',1,'2025-10-29 22:27:16'),(5,'Consultoría Tecnológica',1,'Asesoría en implementación de nuevas tecnologías',1,'2025-10-29 22:27:16'),(6,'Investigación Aplicada',1,'Proyectos de investigación y desarrollo tecnológico',1,'2025-10-29 22:27:16'),(7,'Capacitación Técnica',3,'Cursos y talleres de capacitación especializada',1,'2025-10-29 22:27:16'),(8,'Soporte Especializado',3,'Soporte técnico para sistemas y equipos',1,'2025-10-29 22:27:16'),(9,'Asesoría Profesional',3,'Asesoría y consultoría técnica especializada',1,'2025-10-29 22:27:16');
/*!40000 ALTER TABLE `servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
-- Table structure for table `solicitudes_servicios`
--

DROP TABLE IF EXISTS `solicitudes_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes_servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombres` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo_electronico` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad_procedencia_id` int DEFAULT NULL,
  `entidad_otra` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Se llena cuando selecciona "Otra" entidad',
  `servicio_id` int DEFAULT NULL,
  `servicio_otro` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Se llena cuando selecciona "Otro" servicio',
  `coordinacion_id` int NOT NULL,
  `motivo_solicitud` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `estatus` enum('pendiente','en_revision','aprobada','rechazada','completada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usuario_actualizacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas_internas` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_nombres` (`nombres`),
  KEY `idx_apellidos` (`apellido_paterno`,`apellido_materno`),
  KEY `idx_telefono` (`telefono`),
  KEY `idx_correo` (`correo_electronico`),
  KEY `idx_estatus` (`estatus`),
  KEY `idx_fecha` (`fecha_solicitud`),
  KEY `idx_entidad` (`entidad_procedencia_id`),
  KEY `idx_servicio` (`servicio_id`),
  KEY `idx_coordinacion` (`coordinacion_id`),
  CONSTRAINT `solicitudes_servicios_ibfk_1` FOREIGN KEY (`entidad_procedencia_id`) REFERENCES `entidades_procedencia` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `solicitudes_servicios_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `solicitudes_servicios_ibfk_3` FOREIGN KEY (`coordinacion_id`) REFERENCES `coordinaciones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla principal de solicitudes de servicios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes_servicios`
--

LOCK TABLES `solicitudes_servicios` WRITE;
/*!40000 ALTER TABLE `solicitudes_servicios` DISABLE KEYS */;
INSERT INTO `solicitudes_servicios` VALUES (1,'JUAN CARLOS','GONZÁLEZ','PÉREZ','4421234567','juan.gonzalez@upq.edu.mx',1,NULL,1,NULL,2,'Se requiere calibración de 5 equipos de medición del laboratorio de física. Los equipos incluyen balanzas analíticas, termómetros digitales y medidores de presión que serán utilizados en prácticas académicas del próximo semestre.','pendiente','2025-10-29 22:27:16','2025-10-29 22:27:16',NULL,NULL),(2,'MARÍA FERNANDA','RODRÍGUEZ','SÁNCHEZ','4429876543','maria.rodriguez@empresa.mx',NULL,'SECRETARÍA DE COMUNICACIONES Y TRANSPORTES',7,NULL,3,'Solicito capacitación técnica especializada para un equipo de 10 ingenieros en sistemas de transporte inteligente. La capacitación debe cubrir aspectos teóricos y prácticos con duración de 40 horas.','pendiente','2025-10-29 22:27:16','2025-10-29 22:27:16',NULL,NULL),(3,'ROBERTO ALEJANDRO','MARTÍNEZ','LÓPEZ','4425678901','roberto.martinez@cidet.mx',2,NULL,NULL,'AUDITORÍA DE SISTEMAS DE SEGURIDAD VIAL',1,'Requerimos auditoría completa de sistemas de seguridad vial implementados en carreteras estatales. Se necesita evaluar sensores, cámaras y sistemas de alerta temprana instalados en 50 kilómetros de autopista.','pendiente','2025-10-29 22:27:16','2025-10-29 22:27:16',NULL,NULL),(4,'ANA LAURA','HERNÁNDEZ',NULL,'4423456789','ana.hernandez@gobierno.mx',NULL,'DIRECCIÓN GENERAL DE TRANSPORTE FEDERAL',NULL,'IMPLEMENTACIÓN DE SISTEMA DE MONITOREO GPS',4,'Se solicita implementación de sistema de monitoreo GPS para flota vehicular de 200 unidades. El sistema debe incluir seguimiento en tiempo real, geofencing, reportes automáticos y alertas de mantenimiento preventivo.','en_revision','2025-10-29 22:27:16','2025-10-29 22:27:16',NULL,NULL);
/*!40000 ALTER TABLE `solicitudes_servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_estadisticas_coordinaciones`
--

DROP TABLE IF EXISTS `vista_estadisticas_coordinaciones`;
/*!50001 DROP VIEW IF EXISTS `vista_estadisticas_coordinaciones`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_estadisticas_coordinaciones` AS SELECT 
 1 AS `coordinacion`,
 1 AS `total_solicitudes`,
 1 AS `pendientes`,
 1 AS `en_revision`,
 1 AS `aprobadas`,
 1 AS `rechazadas`,
 1 AS `completadas`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_servicios_populares`
--

DROP TABLE IF EXISTS `vista_servicios_populares`;
/*!50001 DROP VIEW IF EXISTS `vista_servicios_populares`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_servicios_populares` AS SELECT 
 1 AS `servicio`,
 1 AS `coordinacion`,
 1 AS `cantidad_solicitudes`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_solicitudes_completas`
--

DROP TABLE IF EXISTS `vista_solicitudes_completas`;
/*!50001 DROP VIEW IF EXISTS `vista_solicitudes_completas`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_solicitudes_completas` AS SELECT 
 1 AS `id`,
 1 AS `solicitante`,
 1 AS `telefono`,
 1 AS `correo_electronico`,
 1 AS `entidad`,
 1 AS `servicio`,
 1 AS `coordinacion`,
 1 AS `motivo_solicitud`,
 1 AS `estatus`,
 1 AS `fecha_solicitud`,
 1 AS `fecha_solicitud_formato`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'solicitud_servicios'
--

--
-- Dumping routines for database 'solicitud_servicios'
--
/*!50003 DROP FUNCTION IF EXISTS `fn_obtener_coordinacion_servicio` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_obtener_coordinacion_servicio`(p_servicio_id INT) RETURNS int
    READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE v_coordinacion_id INT;
  
  SELECT coordinacion_predeterminada_id 
  INTO v_coordinacion_id
  FROM servicios 
  WHERE id = p_servicio_id;
  
  RETURN IFNULL(v_coordinacion_id, 1);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_crear_solicitud` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_crear_solicitud`(
  IN p_nombres VARCHAR(60),
  IN p_apellido_paterno VARCHAR(60),
  IN p_apellido_materno VARCHAR(60),
  IN p_telefono VARCHAR(10),
  IN p_correo VARCHAR(100),
  IN p_entidad_id INT,
  IN p_entidad_otra VARCHAR(200),
  IN p_servicio_id INT,
  IN p_servicio_otro VARCHAR(200),
  IN p_coordinacion_id INT,
  IN p_motivo LONGTEXT,
  OUT p_solicitud_id INT,
  OUT p_mensaje VARCHAR(255)
)
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    SET p_solicitud_id = -1;
    SET p_mensaje = 'Error al crear la solicitud';
  END;
  
  START TRANSACTION;
  
  -- Validar que teléfono tenga 10 dígitos
  IF CHAR_LENGTH(p_telefono) != 10 THEN
    SET p_solicitud_id = -1;
    SET p_mensaje = 'El teléfono debe tener exactamente 10 dígitos';
    ROLLBACK;
  ELSE
    -- Insertar la solicitud
    INSERT INTO solicitudes_servicios (
      nombres, apellido_paterno, apellido_materno,
      telefono, correo_electronico,
      entidad_procedencia_id, entidad_otra,
      servicio_id, servicio_otro,
      coordinacion_id, motivo_solicitud
    ) VALUES (
      p_nombres, p_apellido_paterno, p_apellido_materno,
      p_telefono, p_correo,
      p_entidad_id, p_entidad_otra,
      p_servicio_id, p_servicio_otro,
      p_coordinacion_id, p_motivo
    );
    
    SET p_solicitud_id = LAST_INSERT_ID();
    SET p_mensaje = 'Solicitud creada exitosamente';
    COMMIT;
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `vista_estadisticas_coordinaciones`
--

/*!50001 DROP VIEW IF EXISTS `vista_estadisticas_coordinaciones`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_estadisticas_coordinaciones` AS select `c`.`nombre` AS `coordinacion`,count(`s`.`id`) AS `total_solicitudes`,sum((case when (`s`.`estatus` = 'pendiente') then 1 else 0 end)) AS `pendientes`,sum((case when (`s`.`estatus` = 'en_revision') then 1 else 0 end)) AS `en_revision`,sum((case when (`s`.`estatus` = 'aprobada') then 1 else 0 end)) AS `aprobadas`,sum((case when (`s`.`estatus` = 'rechazada') then 1 else 0 end)) AS `rechazadas`,sum((case when (`s`.`estatus` = 'completada') then 1 else 0 end)) AS `completadas` from (`coordinaciones` `c` left join `solicitudes_servicios` `s` on((`c`.`id` = `s`.`coordinacion_id`))) group by `c`.`id`,`c`.`nombre` order by `total_solicitudes` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_servicios_populares`
--

/*!50001 DROP VIEW IF EXISTS `vista_servicios_populares`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_servicios_populares` AS select coalesce(`s`.`servicio_otro`,`sv`.`nombre`) AS `servicio`,`c`.`nombre` AS `coordinacion`,count(`s`.`id`) AS `cantidad_solicitudes` from ((`solicitudes_servicios` `s` left join `servicios` `sv` on((`s`.`servicio_id` = `sv`.`id`))) join `coordinaciones` `c` on((`s`.`coordinacion_id` = `c`.`id`))) group by coalesce(`s`.`servicio_otro`,`sv`.`nombre`),`c`.`nombre` order by `cantidad_solicitudes` desc limit 10 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_solicitudes_completas`
--

/*!50001 DROP VIEW IF EXISTS `vista_solicitudes_completas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_solicitudes_completas` AS select `s`.`id` AS `id`,concat(`s`.`nombres`,' ',`s`.`apellido_paterno`,ifnull(concat(' ',`s`.`apellido_materno`),'')) AS `solicitante`,`s`.`telefono` AS `telefono`,`s`.`correo_electronico` AS `correo_electronico`,coalesce(`s`.`entidad_otra`,`e`.`nombre`,'N/A') AS `entidad`,coalesce(`s`.`servicio_otro`,`sv`.`nombre`,'N/A') AS `servicio`,`c`.`nombre` AS `coordinacion`,`s`.`motivo_solicitud` AS `motivo_solicitud`,`s`.`estatus` AS `estatus`,`s`.`fecha_solicitud` AS `fecha_solicitud`,date_format(`s`.`fecha_solicitud`,'%d/%m/%Y %H:%i') AS `fecha_solicitud_formato` from (((`solicitudes_servicios` `s` left join `entidades_procedencia` `e` on((`s`.`entidad_procedencia_id` = `e`.`id`))) left join `servicios` `sv` on((`s`.`servicio_id` = `sv`.`id`))) join `coordinaciones` `c` on((`s`.`coordinacion_id` = `c`.`id`))) order by `s`.`fecha_solicitud` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-29 16:37:31
