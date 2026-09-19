-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para saas_clinica
CREATE DATABASE IF NOT EXISTS `saas_clinica` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `saas_clinica`;

-- Volcando estructura para tabla saas_clinica.auditorias
CREATE TABLE IF NOT EXISTS `auditorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo_id` bigint unsigned DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditorias_clinica_id_index` (`clinica_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.auditorias: ~0 rows (aproximadamente)
DELETE FROM `auditorias`;

-- Volcando estructura para tabla saas_clinica.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.cache: ~1 rows (aproximadamente)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('saas_clinica_cache_config_1', 'a:8:{s:14:"clinica_nombre";s:15:"Clinica Central";s:11:"clinica_nit";s:7:"1234567";s:14:"clinica_ciudad";s:6:"La Paz";s:17:"clinica_direccion";s:14:"Av. Salud #123";s:16:"clinica_telefono";s:14:"+591 2 1234567";s:13:"clinica_email";s:21:"contacto@central.test";s:6:"moneda";s:2:"S/";s:11:"mensaje_pie";s:27:"Gracias por su preferencia.";}', 2097063601);

-- Volcando estructura para tabla saas_clinica.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla saas_clinica.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned NOT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('programada','confirmada','en_espera','atendida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `recordatorio_enviado` tinyint(1) NOT NULL DEFAULT '0',
  `recordatorio_at` timestamp NULL DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `citas_paciente_id_foreign` (`paciente_id`),
  KEY `citas_medico_id_foreign` (`medico_id`),
  KEY `citas_especialidad_id_foreign` (`especialidad_id`),
  KEY `citas_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `citas_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `citas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.citas: ~29 rows (aproximadamente)
DELETE FROM `citas`;
INSERT INTO `citas` (`id`, `clinica_id`, `paciente_id`, `medico_id`, `especialidad_id`, `fecha`, `hora`, `motivo`, `estado`, `recordatorio_enviado`, `recordatorio_at`, `notas`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 1, 3, '2026-06-15', '08:30:00', 'Control cardiologico', 'confirmada', 0, NULL, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 2, 2, 2, '2026-06-15', '09:15:00', 'Consulta pediatrica', 'en_espera', 0, NULL, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(3, 1, 3, 4, 1, '2026-06-15', '10:00:00', 'Chequeo general', 'atendida', 0, NULL, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(4, 1, 4, 3, 5, '2026-06-15', '11:30:00', 'Revision de piel', 'cancelada', 0, NULL, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(5, 1, 5, 1, 3, '2026-06-15', '12:00:00', 'Electrocardiograma', 'programada', 0, NULL, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(6, 1, 1, 1, 3, '2025-11-24', '08:30:00', 'Control general', 'programada', 0, NULL, NULL, '2026-06-27 13:29:54', '2026-06-27 13:29:54', NULL),
	(7, 1, 2, 2, 2, '2025-12-05', '09:15:00', 'Consulta de rutina', 'confirmada', 0, NULL, NULL, '2026-06-27 13:29:54', '2026-06-27 13:29:54', NULL),
	(8, 1, 3, 3, 5, '2026-01-16', '10:00:00', 'Chequeo anual', 'en_espera', 0, NULL, NULL, '2026-06-27 13:29:54', '2026-06-27 13:29:54', NULL),
	(9, 1, 4, 4, 1, '2026-02-11', '11:30:00', 'Seguimiento', 'atendida', 0, NULL, NULL, '2026-06-27 13:29:54', '2026-06-27 13:29:54', NULL),
	(10, 1, 5, 5, 6, '2026-03-04', '12:00:00', 'Dolor abdominal', 'atendida', 0, NULL, NULL, '2026-06-27 13:29:54', '2026-06-27 13:29:54', NULL),
	(11, 1, 6, 6, 7, '2026-04-22', '14:30:00', 'Revision de examenes', 'cancelada', 0, NULL, NULL, '2026-06-27 13:29:54', '2026-06-27 13:29:54', NULL),
	(12, 1, 7, 7, 8, '2026-05-07', '15:45:00', 'Control postoperatorio', 'programada', 0, NULL, NULL, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(13, 1, 8, 8, 9, '2026-06-25', '16:30:00', 'Consulta especializada', 'confirmada', 0, NULL, NULL, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(14, 1, 9, 9, 10, '2026-06-27', '08:30:00', 'Control general', 'en_espera', 0, NULL, NULL, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(15, 1, 10, 10, 1, '2026-06-27', '09:15:00', 'Consulta de rutina', 'atendida', 0, NULL, NULL, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(16, 1, 11, 1, 3, '2026-06-27', '10:00:00', 'Chequeo anual', 'atendida', 0, NULL, NULL, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(17, 1, 12, 2, 2, '2026-06-27', '11:30:00', 'Seguimiento', 'cancelada', 0, NULL, NULL, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(18, 1, 1, 1, 3, '2025-12-23', '08:30:00', 'Control general', 'programada', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(19, 1, 2, 2, 2, '2026-01-26', '09:15:00', 'Consulta de rutina', 'confirmada', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(20, 1, 3, 3, 5, '2026-02-09', '10:00:00', 'Chequeo anual', 'en_espera', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(21, 1, 4, 4, 1, '2026-03-06', '11:30:00', 'Seguimiento', 'atendida', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(22, 1, 5, 5, 6, '2026-04-16', '12:00:00', 'Dolor abdominal', 'atendida', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(23, 1, 6, 6, 7, '2026-05-04', '14:30:00', 'Revision de examenes', 'cancelada', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(24, 1, 7, 7, 8, '2026-06-03', '15:45:00', 'Control postoperatorio', 'programada', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(25, 1, 8, 8, 9, '2026-07-17', '16:30:00', 'Consulta especializada', 'confirmada', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(26, 1, 9, 9, 10, '2026-07-06', '08:30:00', 'Control general', 'en_espera', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(27, 1, 10, 10, 1, '2026-07-06', '09:15:00', 'Consulta de rutina', 'atendida', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(28, 1, 11, 1, 3, '2026-07-06', '10:00:00', 'Chequeo anual', 'atendida', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(29, 1, 12, 2, 2, '2026-07-06', '11:30:00', 'Seguimiento', 'cancelada', 0, NULL, NULL, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL);

-- Volcando estructura para tabla saas_clinica.clinicas
CREATE TABLE IF NOT EXISTS `clinicas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#7c44ff',
  `plan_id` bigint unsigned DEFAULT NULL,
  `estado` enum('prueba','activa','suspendida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prueba',
  `fecha_inicio` date DEFAULT NULL,
  `trial_ends_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clinicas_slug_unique` (`slug`),
  KEY `clinicas_plan_id_foreign` (`plan_id`),
  CONSTRAINT `clinicas_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.clinicas: ~4 rows (aproximadamente)
DELETE FROM `clinicas`;
INSERT INTO `clinicas` (`id`, `nombre`, `slug`, `nit`, `email`, `telefono`, `direccion`, `ciudad`, `color`, `plan_id`, `estado`, `fecha_inicio`, `trial_ends_at`, `created_at`, `updated_at`) VALUES
	(1, 'Clinica Central', 'clinica-central', '1234567', 'contacto@central.test', '+591 2 1234567', 'Av. Salud #123', 'La Paz', '#7c44ff', 2, 'activa', '2026-02-15', NULL, '2026-06-15 06:22:20', '2026-06-15 06:22:20'),
	(2, 'Clinica San Rafael', 'clinica-san-rafael', NULL, 'admin@sanrafael.test', NULL, NULL, 'Cochabamba', '#0891b2', 1, 'activa', '2026-03-15', NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(3, 'Centro Medico Vida', 'centro-medico-vida', NULL, 'admin@vida.test', NULL, NULL, 'Santa Cruz', '#16a34a', 3, 'prueba', '2026-04-15', NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(4, 'Policlinico Norte', 'policlinico-norte', NULL, 'admin@norte.test', NULL, NULL, 'El Alto', '#db2777', 1, 'suspendida', '2026-04-15', NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21');

-- Volcando estructura para tabla saas_clinica.configuraciones
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `clave` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_clinica_id_clave_unique` (`clinica_id`,`clave`),
  CONSTRAINT `configuraciones_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.configuraciones: ~8 rows (aproximadamente)
DELETE FROM `configuraciones`;
INSERT INTO `configuraciones` (`id`, `clinica_id`, `clave`, `valor`, `created_at`, `updated_at`) VALUES
	(1, 1, 'clinica_nombre', 'Clinica Central', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(2, 1, 'clinica_nit', '1234567', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(3, 1, 'clinica_ciudad', 'La Paz', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(4, 1, 'clinica_direccion', 'Av. Salud #123', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(5, 1, 'clinica_telefono', '+591 2 1234567', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(6, 1, 'clinica_email', 'contacto@central.test', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(7, 1, 'moneda', 'S/', '2026-06-15 06:22:21', '2026-06-15 14:34:17'),
	(8, 1, 'mensaje_pie', 'Gracias por su preferencia.', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(9, 1, 'demo_data_seeded', '2026-06-27 08:29:55', '2026-06-27 13:29:55', '2026-06-27 13:29:55');

-- Volcando estructura para tabla saas_clinica.especialidades
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tarifa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#7c44ff',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `especialidades_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `especialidades_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.especialidades: ~10 rows (aproximadamente)
DELETE FROM `especialidades`;
INSERT INTO `especialidades` (`id`, `clinica_id`, `nombre`, `descripcion`, `tarifa`, `color`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'Medicina General', NULL, 150.00, '#7c44ff', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 'Pediatria', NULL, 180.00, '#38bdf8', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(3, 1, 'Cardiologia', NULL, 350.00, '#34d399', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(4, 1, 'Odontologia', NULL, 200.00, '#fbbf24', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(5, 1, 'Dermatologia', NULL, 250.00, '#fb7185', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(6, 1, 'Ginecologia', NULL, 220.00, '#a855f7', 1, '2026-06-27 13:29:53', '2026-06-27 13:29:53', NULL),
	(7, 1, 'Traumatologia', NULL, 280.00, '#f97316', 1, '2026-06-27 13:29:53', '2026-06-27 13:29:53', NULL),
	(8, 1, 'Oftalmologia', NULL, 240.00, '#0ea5e9', 1, '2026-06-27 13:29:53', '2026-06-27 13:29:53', NULL),
	(9, 1, 'Neurologia', NULL, 360.00, '#14b8a6', 1, '2026-06-27 13:29:53', '2026-06-27 13:29:53', NULL),
	(10, 1, 'Otorrinolaringologia', NULL, 230.00, '#ef4444', 1, '2026-06-27 13:29:53', '2026-06-27 13:29:53', NULL);

-- Volcando estructura para tabla saas_clinica.facturas
CREATE TABLE IF NOT EXISTS `facturas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_comprobante` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correlativo` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda_iso` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `paciente_id` bigint unsigned NOT NULL,
  `cita_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('pendiente','pagada','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `sunat_estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no_enviado',
  `sunat_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sunat_codigo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sunat_mensaje` text COLLATE utf8mb4_unicode_ci,
  `sunat_ticket` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xml_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdr_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enviado_at` timestamp NULL DEFAULT NULL,
  `metodo_pago` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facturas_numero_unique` (`numero`),
  KEY `facturas_paciente_id_foreign` (`paciente_id`),
  KEY `facturas_cita_id_foreign` (`cita_id`),
  KEY `facturas_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `facturas_cita_id_foreign` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `facturas_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `facturas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.facturas: ~25 rows (aproximadamente)
DELETE FROM `facturas`;
INSERT INTO `facturas` (`id`, `clinica_id`, `numero`, `tipo_comprobante`, `serie`, `correlativo`, `moneda_iso`, `paciente_id`, `cita_id`, `fecha`, `subtotal`, `descuento`, `total`, `igv`, `estado`, `sunat_estado`, `sunat_hash`, `sunat_codigo`, `sunat_mensaje`, `sunat_ticket`, `xml_path`, `cdr_path`, `enviado_at`, `metodo_pago`, `notas`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'FAC-00001', NULL, NULL, NULL, 'PEN', 1, NULL, '2026-06-15', 350.00, 0.00, 350.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'efectivo', NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 'FAC-00002', NULL, NULL, NULL, 'PEN', 1, NULL, '2026-01-21', 300.00, 30.00, 270.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'efectivo', 'Atencion Medicina General', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(3, 1, 'FAC-00003', NULL, NULL, NULL, 'PEN', 2, NULL, '2026-01-17', 180.00, 0.00, 180.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tarjeta', 'Atencion Pediatria', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(4, 1, 'FAC-00004', NULL, NULL, NULL, 'PEN', 3, NULL, '2026-02-05', 700.00, 0.00, 700.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'transferencia', 'Atencion Cardiologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(5, 1, 'FAC-00005', NULL, NULL, NULL, 'PEN', 4, NULL, '2026-02-04', 400.00, 0.00, 400.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'seguro', 'Atencion Odontologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(6, 1, 'FAC-00006', NULL, NULL, NULL, 'PEN', 5, NULL, '2026-03-23', 500.00, 50.00, 450.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'efectivo', 'Atencion Dermatologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(7, 1, 'FAC-00007', NULL, NULL, NULL, 'PEN', 6, NULL, '2026-03-13', 220.00, 0.00, 220.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tarjeta', 'Atencion Ginecologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(8, 1, 'FAC-00008', NULL, NULL, NULL, 'PEN', 7, NULL, '2026-04-08', 280.00, 0.00, 280.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'transferencia', 'Atencion Traumatologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(9, 1, 'FAC-00009', NULL, NULL, NULL, 'PEN', 8, NULL, '2026-04-16', 240.00, 0.00, 240.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'seguro', 'Atencion Oftalmologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(10, 1, 'FAC-00010', NULL, NULL, NULL, 'PEN', 9, NULL, '2026-05-04', 720.00, 72.00, 648.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'efectivo', 'Atencion Neurologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(11, 1, 'FAC-00011', NULL, NULL, NULL, 'PEN', 10, NULL, '2026-05-20', 230.00, 0.00, 230.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tarjeta', 'Atencion Otorrinolaringologia', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(12, 1, 'FAC-00012', NULL, NULL, NULL, 'PEN', 11, NULL, '2026-06-27', 300.00, 0.00, 300.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'transferencia', 'Atencion Medicina General', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(13, 1, 'FAC-00013', NULL, NULL, NULL, 'PEN', 12, NULL, '2026-06-27', 180.00, 0.00, 180.00, 0.00, 'pendiente', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'seguro', 'Atencion Pediatria', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(14, 1, 'FAC-00014', NULL, NULL, NULL, 'PEN', 1, NULL, '2026-02-08', 300.00, 30.00, 270.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'efectivo', 'Atencion Medicina General', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(15, 1, 'FAC-00015', NULL, NULL, NULL, 'PEN', 2, NULL, '2026-02-02', 360.00, 0.00, 360.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tarjeta', 'Atencion Pediatria', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(16, 1, 'FAC-00016', NULL, NULL, NULL, 'PEN', 3, NULL, '2026-03-19', 700.00, 0.00, 700.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'transferencia', 'Atencion Cardiologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(17, 1, 'FAC-00017', NULL, NULL, NULL, 'PEN', 4, NULL, '2026-03-04', 400.00, 0.00, 400.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'seguro', 'Atencion Odontologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(18, 1, 'FAC-00018', NULL, NULL, NULL, 'PEN', 5, NULL, '2026-04-10', 250.00, 25.00, 225.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'efectivo', 'Atencion Dermatologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(19, 1, 'FAC-00019', NULL, NULL, NULL, 'PEN', 6, NULL, '2026-04-10', 440.00, 0.00, 440.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tarjeta', 'Atencion Ginecologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(20, 1, 'FAC-00020', NULL, NULL, NULL, 'PEN', 7, NULL, '2026-05-03', 560.00, 0.00, 560.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'transferencia', 'Atencion Traumatologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(21, 1, 'FAC-00021', NULL, NULL, NULL, 'PEN', 8, NULL, '2026-05-22', 240.00, 0.00, 240.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'seguro', 'Atencion Oftalmologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(22, 1, 'FAC-00022', NULL, NULL, NULL, 'PEN', 9, NULL, '2026-06-14', 360.00, 36.00, 324.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'efectivo', 'Atencion Neurologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(23, 1, 'FAC-00023', NULL, NULL, NULL, 'PEN', 10, NULL, '2026-06-21', 460.00, 0.00, 460.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tarjeta', 'Atencion Otorrinolaringologia', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(24, 1, 'FAC-00024', NULL, NULL, NULL, 'PEN', 11, NULL, '2026-07-06', 150.00, 0.00, 150.00, 0.00, 'pagada', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'transferencia', 'Atencion Medicina General', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(25, 1, 'FAC-00025', NULL, NULL, NULL, 'PEN', 12, NULL, '2026-07-06', 180.00, 0.00, 180.00, 0.00, 'pendiente', 'no_enviado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'seguro', 'Atencion Pediatria', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL);

-- Volcando estructura para tabla saas_clinica.factura_items
CREATE TABLE IF NOT EXISTS `factura_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `factura_id` bigint unsigned NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(8,2) NOT NULL DEFAULT '1.00',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_items_factura_id_foreign` (`factura_id`),
  CONSTRAINT `factura_items_factura_id_foreign` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.factura_items: ~25 rows (aproximadamente)
DELETE FROM `factura_items`;
INSERT INTO `factura_items` (`id`, `factura_id`, `descripcion`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Consulta Cardiologia', 1.00, 350.00, 350.00, '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(2, 2, 'Consulta Medicina General', 2.00, 150.00, 300.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(3, 3, 'Consulta Pediatria', 1.00, 180.00, 180.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(4, 4, 'Consulta Cardiologia', 2.00, 350.00, 700.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(5, 5, 'Consulta Odontologia', 2.00, 200.00, 400.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(6, 6, 'Consulta Dermatologia', 2.00, 250.00, 500.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(7, 7, 'Consulta Ginecologia', 1.00, 220.00, 220.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(8, 8, 'Consulta Traumatologia', 1.00, 280.00, 280.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(9, 9, 'Consulta Oftalmologia', 1.00, 240.00, 240.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(10, 10, 'Consulta Neurologia', 2.00, 360.00, 720.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(11, 11, 'Consulta Otorrinolaringologia', 1.00, 230.00, 230.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(12, 12, 'Consulta Medicina General', 2.00, 150.00, 300.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(13, 13, 'Consulta Pediatria', 1.00, 180.00, 180.00, '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(14, 14, 'Consulta Medicina General', 2.00, 150.00, 300.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(15, 15, 'Consulta Pediatria', 2.00, 180.00, 360.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(16, 16, 'Consulta Cardiologia', 2.00, 350.00, 700.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(17, 17, 'Consulta Odontologia', 2.00, 200.00, 400.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(18, 18, 'Consulta Dermatologia', 1.00, 250.00, 250.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(19, 19, 'Consulta Ginecologia', 2.00, 220.00, 440.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(20, 20, 'Consulta Traumatologia', 2.00, 280.00, 560.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(21, 21, 'Consulta Oftalmologia', 1.00, 240.00, 240.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(22, 22, 'Consulta Neurologia', 1.00, 360.00, 360.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(23, 23, 'Consulta Otorrinolaringologia', 2.00, 230.00, 460.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(24, 24, 'Consulta Medicina General', 1.00, 150.00, 150.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(25, 25, 'Consulta Pediatria', 1.00, 180.00, 180.00, '2026-07-06 14:46:22', '2026-07-06 14:46:22');

-- Volcando estructura para tabla saas_clinica.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
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

-- Volcando datos para la tabla saas_clinica.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla saas_clinica.historias_clinicas
CREATE TABLE IF NOT EXISTS `historias_clinicas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `motivo_consulta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sintomas` text COLLATE utf8mb4_unicode_ci,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `tratamiento` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `presion_arterial` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `frecuencia_cardiaca` smallint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historias_clinicas_paciente_id_foreign` (`paciente_id`),
  KEY `historias_clinicas_medico_id_foreign` (`medico_id`),
  KEY `historias_clinicas_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `historias_clinicas_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `historias_clinicas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `historias_clinicas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.historias_clinicas: ~21 rows (aproximadamente)
DELETE FROM `historias_clinicas`;
INSERT INTO `historias_clinicas` (`id`, `clinica_id`, `paciente_id`, `medico_id`, `fecha`, `motivo_consulta`, `sintomas`, `diagnostico`, `tratamiento`, `observaciones`, `peso`, `talla`, `presion_arterial`, `temperatura`, `frecuencia_cardiaca`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 1, '2026-06-12', 'Dolor toracico leve', 'Molestia al esfuerzo', 'Hipertension controlada', 'Continuar medicacion', NULL, 78.50, 172.00, '130/85', 36.6, 78, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 1, 1, '2025-11-24', 'Control general', 'Sintomas referidos por el paciente', 'Hipertension controlada', 'Tratamiento indicado segun diagnostico', NULL, 60.50, 163.00, '120/85', 36.4, 79, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(3, 1, 2, 2, '2025-12-08', 'Consulta de rutina', 'Sintomas referidos por el paciente', 'Faringitis aguda', 'Tratamiento indicado segun diagnostico', NULL, 68.50, 177.00, '100/71', 36.3, 86, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(4, 1, 3, 3, '2026-01-06', 'Chequeo anual', 'Sintomas referidos por el paciente', 'Gastritis', 'Tratamiento indicado segun diagnostico', NULL, 88.50, 152.00, '121/82', 36.0, 73, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(5, 1, 4, 4, '2026-02-22', 'Seguimiento', 'Sintomas referidos por el paciente', 'Migrana', 'Tratamiento indicado segun diagnostico', NULL, 60.50, 156.00, '121/73', 37.3, 68, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(6, 1, 5, 5, '2026-03-25', 'Dolor abdominal', 'Sintomas referidos por el paciente', 'Dermatitis', 'Tratamiento indicado segun diagnostico', NULL, 61.50, 170.00, '133/87', 36.4, 61, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(7, 1, 6, 6, '2026-04-06', 'Revision de examenes', 'Sintomas referidos por el paciente', 'Lumbalgia', 'Tratamiento indicado segun diagnostico', NULL, 64.50, 155.00, '128/61', 36.8, 94, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(8, 1, 7, 7, '2026-05-10', 'Control postoperatorio', 'Sintomas referidos por el paciente', 'Anemia leve', 'Tratamiento indicado segun diagnostico', NULL, 80.50, 156.00, '105/88', 37.5, 95, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(9, 1, 8, 8, '2026-06-17', 'Consulta especializada', 'Sintomas referidos por el paciente', 'Bronquitis', 'Tratamiento indicado segun diagnostico', NULL, 89.50, 154.00, '113/66', 37.3, 92, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(10, 1, 9, 9, '2025-11-16', 'Control general', 'Sintomas referidos por el paciente', 'Alergia estacional', 'Tratamiento indicado segun diagnostico', NULL, 70.50, 162.00, '103/81', 36.6, 82, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(11, 1, 10, 10, '2025-12-10', 'Consulta de rutina', 'Sintomas referidos por el paciente', 'Control sano', 'Tratamiento indicado segun diagnostico', NULL, 65.50, 183.00, '135/79', 36.5, 95, '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(12, 1, 1, 1, '2025-12-04', 'Control general', 'Sintomas referidos por el paciente', 'Hipertension controlada', 'Tratamiento indicado segun diagnostico', NULL, 64.50, 173.00, '123/78', 36.5, 95, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(13, 1, 2, 2, '2026-01-23', 'Consulta de rutina', 'Sintomas referidos por el paciente', 'Faringitis aguda', 'Tratamiento indicado segun diagnostico', NULL, 94.50, 170.00, '110/90', 36.4, 86, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(14, 1, 3, 3, '2026-02-20', 'Chequeo anual', 'Sintomas referidos por el paciente', 'Gastritis', 'Tratamiento indicado segun diagnostico', NULL, 54.50, 153.00, '101/74', 36.7, 89, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(15, 1, 4, 4, '2026-03-18', 'Seguimiento', 'Sintomas referidos por el paciente', 'Migrana', 'Tratamiento indicado segun diagnostico', NULL, 82.50, 175.00, '127/82', 36.0, 93, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(16, 1, 5, 5, '2026-04-25', 'Dolor abdominal', 'Sintomas referidos por el paciente', 'Dermatitis', 'Tratamiento indicado segun diagnostico', NULL, 95.50, 167.00, '118/66', 37.0, 69, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(17, 1, 6, 6, '2026-05-12', 'Revision de examenes', 'Sintomas referidos por el paciente', 'Lumbalgia', 'Tratamiento indicado segun diagnostico', NULL, 65.50, 150.00, '113/70', 36.7, 67, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(18, 1, 7, 7, '2026-06-15', 'Control postoperatorio', 'Sintomas referidos por el paciente', 'Anemia leve', 'Tratamiento indicado segun diagnostico', NULL, 80.50, 153.00, '120/73', 36.1, 64, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(19, 1, 8, 8, '2026-07-21', 'Consulta especializada', 'Sintomas referidos por el paciente', 'Bronquitis', 'Tratamiento indicado segun diagnostico', NULL, 79.50, 153.00, '113/89', 36.3, 60, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(20, 1, 9, 9, '2025-12-04', 'Control general', 'Sintomas referidos por el paciente', 'Alergia estacional', 'Tratamiento indicado segun diagnostico', NULL, 80.50, 155.00, '113/66', 37.0, 95, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(21, 1, 10, 10, '2026-01-26', 'Consulta de rutina', 'Sintomas referidos por el paciente', 'Control sano', 'Tratamiento indicado segun diagnostico', NULL, 74.50, 164.00, '120/70', 36.0, 79, '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL);

-- Volcando estructura para tabla saas_clinica.historia_archivos
CREATE TABLE IF NOT EXISTS `historia_archivos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `historia_clinica_id` bigint unsigned NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamano` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historia_archivos_historia_clinica_id_foreign` (`historia_clinica_id`),
  CONSTRAINT `historia_archivos_historia_clinica_id_foreign` FOREIGN KEY (`historia_clinica_id`) REFERENCES `historias_clinicas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.historia_archivos: ~0 rows (aproximadamente)
DELETE FROM `historia_archivos`;

-- Volcando estructura para tabla saas_clinica.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
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

-- Volcando datos para la tabla saas_clinica.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla saas_clinica.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
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

-- Volcando datos para la tabla saas_clinica.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla saas_clinica.laboratorio_items
CREATE TABLE IF NOT EXISTS `laboratorio_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `orden_id` bigint unsigned NOT NULL,
  `examen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resultado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_referencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laboratorio_items_orden_id_foreign` (`orden_id`),
  CONSTRAINT `laboratorio_items_orden_id_foreign` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_laboratorio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.laboratorio_items: ~42 rows (aproximadamente)
DELETE FROM `laboratorio_items`;
INSERT INTO `laboratorio_items` (`id`, `orden_id`, `examen`, `resultado`, `unidad`, `valor_referencia`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Hemograma completo', 'Normal', '', '', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(2, 1, 'Glucosa', '92', 'mg/dL', '70-100', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(3, 2, 'Hemograma completo', 'Normal', '', '', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(4, 2, 'Colesterol total', '189', 'mg/dL', '<200', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(5, 3, 'Glucosa en ayunas', '84', 'mg/dL', '70-100', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(6, 3, 'Trigliceridos', '162', 'mg/dL', '<150', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(7, 4, 'Colesterol total', '189', 'mg/dL', '<200', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(8, 4, 'Creatinina', '0.9', 'mg/dL', '0.6-1.2', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(9, 5, 'Trigliceridos', '162', 'mg/dL', '<150', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(10, 5, 'Hemograma completo', 'Normal', '', '', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(11, 6, 'Creatinina', '0.9', 'mg/dL', '0.6-1.2', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(12, 6, 'Glucosa en ayunas', '84', 'mg/dL', '70-100', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(13, 7, 'Hemograma completo', 'Normal', '', '', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(14, 7, 'Colesterol total', '189', 'mg/dL', '<200', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(15, 8, 'Glucosa en ayunas', '84', 'mg/dL', '70-100', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(16, 8, 'Trigliceridos', '162', 'mg/dL', '<150', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(17, 9, 'Colesterol total', '189', 'mg/dL', '<200', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(18, 9, 'Creatinina', '0.9', 'mg/dL', '0.6-1.2', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(19, 10, 'Trigliceridos', '162', 'mg/dL', '<150', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(20, 10, 'Hemograma completo', 'Normal', '', '', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(21, 11, 'Creatinina', '0.9', 'mg/dL', '0.6-1.2', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(22, 11, 'Glucosa en ayunas', '84', 'mg/dL', '70-100', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(23, 12, 'Hemograma completo', 'Normal', '', '', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(24, 12, 'Colesterol total', '157', 'mg/dL', '<200', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(25, 13, 'Glucosa en ayunas', '106', 'mg/dL', '70-100', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(26, 13, 'Trigliceridos', '104', 'mg/dL', '<150', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(27, 14, 'Colesterol total', '157', 'mg/dL', '<200', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(28, 14, 'Creatinina', '0.7', 'mg/dL', '0.6-1.2', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(29, 15, 'Trigliceridos', '104', 'mg/dL', '<150', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(30, 15, 'Hemograma completo', 'Normal', '', '', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(31, 16, 'Creatinina', '0.7', 'mg/dL', '0.6-1.2', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(32, 16, 'Glucosa en ayunas', '106', 'mg/dL', '70-100', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(33, 17, 'Hemograma completo', 'Normal', '', '', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(34, 17, 'Colesterol total', '157', 'mg/dL', '<200', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(35, 18, 'Glucosa en ayunas', '106', 'mg/dL', '70-100', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(36, 18, 'Trigliceridos', '104', 'mg/dL', '<150', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(37, 19, 'Colesterol total', '157', 'mg/dL', '<200', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(38, 19, 'Creatinina', '0.7', 'mg/dL', '0.6-1.2', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(39, 20, 'Trigliceridos', '104', 'mg/dL', '<150', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(40, 20, 'Hemograma completo', 'Normal', '', '', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(41, 21, 'Creatinina', '0.7', 'mg/dL', '0.6-1.2', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(42, 21, 'Glucosa en ayunas', '106', 'mg/dL', '70-100', '2026-07-06 14:46:22', '2026-07-06 14:46:22');

-- Volcando estructura para tabla saas_clinica.medicos
CREATE TABLE IF NOT EXISTS `medicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `especialidad_id` bigint unsigned DEFAULT NULL,
  `matricula` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medicos_especialidad_id_foreign` (`especialidad_id`),
  KEY `medicos_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `medicos_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medicos_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.medicos: ~10 rows (aproximadamente)
DELETE FROM `medicos`;
INSERT INTO `medicos` (`id`, `clinica_id`, `nombres`, `apellidos`, `especialidad_id`, `matricula`, `email`, `telefono`, `hora_inicio`, `hora_fin`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'Mariana', 'Lopez', 3, 'MED-1001', 'mlopez@clinica.test', '+591 71111111', '08:00:00', '16:00:00', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 'Luis', 'Vargas', 2, 'MED-1002', 'lvargas@clinica.test', '+591 71111112', '08:00:00', '16:00:00', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(3, 1, 'Pablo', 'Soto', 5, 'MED-1003', 'psoto@clinica.test', '+591 71111113', '08:00:00', '16:00:00', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(4, 1, 'Carla', 'Mendez', 1, 'MED-1004', 'cmendez@clinica.test', '+591 71111114', '08:00:00', '16:00:00', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(5, 1, 'Sofia', 'Quispe', 6, 'MED-2001', 'quispe@clinica.test', '+591 72379552', '08:00:00', '17:00:00', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(6, 1, 'Andres', 'Rojas', 7, 'MED-2002', 'rojas@clinica.test', '+591 75502791', '08:00:00', '17:00:00', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(7, 1, 'Valeria', 'Flores', 8, 'MED-2003', 'flores@clinica.test', '+591 71859241', '08:00:00', '17:00:00', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(8, 1, 'Diego', 'Castro', 9, 'MED-2004', 'castro@clinica.test', '+591 74859355', '08:00:00', '17:00:00', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(9, 1, 'Camila', 'Herrera', 10, 'MED-2005', 'herrera@clinica.test', '+591 72138638', '08:00:00', '17:00:00', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(10, 1, 'Jorge', 'Salinas', 1, 'MED-2006', 'salinas@clinica.test', '+591 78638654', '08:00:00', '17:00:00', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL);

-- Volcando estructura para tabla saas_clinica.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.migrations: ~0 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_06_14_100000_create_especialidades_table', 1),
	(5, '2026_06_14_100100_create_medicos_table', 1),
	(6, '2026_06_14_100200_create_pacientes_table', 1),
	(7, '2026_06_14_100300_create_citas_table', 1),
	(8, '2026_06_14_100400_create_historias_clinicas_table', 1),
	(9, '2026_06_14_100500_create_recetas_table', 1),
	(10, '2026_06_14_100600_create_facturas_table', 1),
	(11, '2026_06_14_100700_create_ordenes_laboratorio_table', 1),
	(12, '2026_06_14_100800_create_productos_table', 1),
	(13, '2026_06_14_100900_create_configuraciones_table', 1),
	(14, '2026_06_14_101000_create_planes_table', 1),
	(15, '2026_06_14_101001_create_clinicas_table', 1),
	(16, '2026_06_14_101002_create_suscripciones_table', 1),
	(17, '2026_06_14_101003_add_clinica_id_to_tables', 1),
	(18, '2026_06_14_101100_add_soft_deletes_to_tables', 2),
	(19, '2026_06_14_101101_create_auditorias_table', 2),
	(20, '2026_06_14_101102_create_historia_archivos_table', 2),
	(21, '2026_06_14_101103_add_recordatorio_to_citas', 2),
	(22, '2026_08_06_000001_add_sunat_to_facturas', 3);

-- Volcando estructura para tabla saas_clinica.movimientos_inventario
CREATE TABLE IF NOT EXISTS `movimientos_inventario` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('entrada','salida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_inventario_producto_id_foreign` (`producto_id`),
  KEY `movimientos_inventario_user_id_foreign` (`user_id`),
  CONSTRAINT `movimientos_inventario_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_inventario_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.movimientos_inventario: ~20 rows (aproximadamente)
DELETE FROM `movimientos_inventario`;
INSERT INTO `movimientos_inventario` (`id`, `producto_id`, `user_id`, `tipo`, `cantidad`, `motivo`, `fecha`, `created_at`, `updated_at`) VALUES
	(1, 5, NULL, 'entrada', 80, 'Compra inicial de inventario', '2026-06-17 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(2, 6, NULL, 'entrada', 45, 'Compra inicial de inventario', '2026-05-06 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(3, 7, NULL, 'entrada', 30, 'Compra inicial de inventario', '2026-04-08 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(4, 8, NULL, 'entrada', 120, 'Compra inicial de inventario', '2026-03-25 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(5, 9, NULL, 'entrada', 18, 'Compra inicial de inventario', '2026-02-12 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(6, 10, NULL, 'entrada', 200, 'Compra inicial de inventario', '2026-01-05 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(7, 11, NULL, 'entrada', 12, 'Compra inicial de inventario', '2026-06-09 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(8, 12, NULL, 'entrada', 25, 'Compra inicial de inventario', '2026-05-22 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(9, 13, NULL, 'entrada', 90, 'Compra inicial de inventario', '2026-04-10 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(10, 14, NULL, 'entrada', 60, 'Compra inicial de inventario', '2026-03-14 05:00:00', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(11, 5, NULL, 'entrada', 80, 'Compra inicial de inventario', '2026-07-07 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(12, 6, NULL, 'entrada', 45, 'Compra inicial de inventario', '2026-06-09 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(13, 7, NULL, 'entrada', 30, 'Compra inicial de inventario', '2026-05-19 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(14, 8, NULL, 'entrada', 120, 'Compra inicial de inventario', '2026-04-26 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(15, 9, NULL, 'entrada', 18, 'Compra inicial de inventario', '2026-03-08 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(16, 10, NULL, 'entrada', 200, 'Compra inicial de inventario', '2026-02-03 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(17, 11, NULL, 'entrada', 12, 'Compra inicial de inventario', '2026-07-11 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(18, 12, NULL, 'entrada', 25, 'Compra inicial de inventario', '2026-06-23 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(19, 13, NULL, 'entrada', 90, 'Compra inicial de inventario', '2026-05-20 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(20, 14, NULL, 'entrada', 60, 'Compra inicial de inventario', '2026-04-09 05:00:00', '2026-07-06 14:46:22', '2026-07-06 14:46:22');

-- Volcando estructura para tabla saas_clinica.ordenes_laboratorio
CREATE TABLE IF NOT EXISTS `ordenes_laboratorio` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` enum('solicitada','en_proceso','completada','entregada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solicitada',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordenes_laboratorio_numero_unique` (`numero`),
  KEY `ordenes_laboratorio_paciente_id_foreign` (`paciente_id`),
  KEY `ordenes_laboratorio_medico_id_foreign` (`medico_id`),
  KEY `ordenes_laboratorio_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `ordenes_laboratorio_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenes_laboratorio_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenes_laboratorio_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.ordenes_laboratorio: ~21 rows (aproximadamente)
DELETE FROM `ordenes_laboratorio`;
INSERT INTO `ordenes_laboratorio` (`id`, `clinica_id`, `numero`, `paciente_id`, `medico_id`, `fecha`, `estado`, `observaciones`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'LAB-00001', 2, 2, '2026-06-15', 'completada', 'Ayuno de 8 horas.', '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 'LAB-00002', 1, 1, '2025-11-26', 'solicitada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(3, 1, 'LAB-00003', 2, 2, '2025-12-17', 'en_proceso', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(4, 1, 'LAB-00004', 3, 3, '2026-01-04', 'completada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(5, 1, 'LAB-00005', 4, 4, '2026-02-12', 'completada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(6, 1, 'LAB-00006', 5, 5, '2026-03-12', 'entregada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(7, 1, 'LAB-00007', 6, 6, '2026-04-12', 'solicitada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(8, 1, 'LAB-00008', 7, 7, '2026-05-12', 'en_proceso', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(9, 1, 'LAB-00009', 8, 8, '2026-06-19', 'completada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(10, 1, 'LAB-00010', 9, 9, '2025-11-17', 'completada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(11, 1, 'LAB-00011', 10, 10, '2025-12-15', 'entregada', 'Ayuno de 8 horas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(12, 1, 'LAB-00012', 1, 1, '2025-12-21', 'solicitada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(13, 1, 'LAB-00013', 2, 2, '2026-01-27', 'en_proceso', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(14, 1, 'LAB-00014', 3, 3, '2026-02-04', 'completada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(15, 1, 'LAB-00015', 4, 4, '2026-03-23', 'completada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(16, 1, 'LAB-00016', 5, 5, '2026-04-15', 'entregada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(17, 1, 'LAB-00017', 6, 6, '2026-05-20', 'solicitada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(18, 1, 'LAB-00018', 7, 7, '2026-06-06', 'en_proceso', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(19, 1, 'LAB-00019', 8, 8, '2026-07-27', 'completada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(20, 1, 'LAB-00020', 9, 9, '2025-12-16', 'completada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(21, 1, 'LAB-00021', 10, 10, '2026-01-21', 'entregada', 'Ayuno de 8 horas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL);

-- Volcando estructura para tabla saas_clinica.pacientes
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Documento de identidad',
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('M','F','O') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grupo_sanguineo` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `seguro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pacientes_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `pacientes_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.pacientes: ~15 rows (aproximadamente)
DELETE FROM `pacientes`;
INSERT INTO `pacientes` (`id`, `clinica_id`, `nombres`, `apellidos`, `ci`, `fecha_nacimiento`, `sexo`, `telefono`, `email`, `direccion`, `grupo_sanguineo`, `alergias`, `seguro`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'Juan', 'Perez', '8451236', '1988-04-12', 'M', '+591 70011111', NULL, NULL, 'O+', NULL, 'Particular', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 'Ana', 'Torres', '7745120', '1995-09-03', 'F', '+591 70022222', NULL, NULL, 'A+', NULL, 'Particular', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(3, 1, 'Carlos', 'Ruiz', '9123450', '1979-12-21', 'M', '+591 70033333', NULL, NULL, 'B+', NULL, 'Particular', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(4, 1, 'Lucia', 'Mamani', '6634578', '2001-06-30', 'F', '+591 70044444', NULL, NULL, 'O-', NULL, 'Particular', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(5, 1, 'Pedro', 'Gomez', '5523419', '1990-01-15', 'M', '+591 70055555', NULL, NULL, 'AB+', NULL, 'Particular', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(6, 1, 'Mateo', 'Choque', '9000001', '1986-09-10', 'M', '+591 64365547', 'mateo.choque@mail.test', 'Zona 1, calle 45', 'O+', NULL, 'Particular', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(7, 1, 'Valentina', 'Aguilar', '9000002', '2010-07-27', 'F', '+591 62524635', 'valentina.aguilar@mail.test', 'Zona 2, calle 34', 'A+', NULL, 'Seguro Privado', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(8, 1, 'Sebastian', 'Cespedes', '9000003', '1996-02-19', 'M', '+591 68780432', 'sebastian.cespedes@mail.test', 'Zona 3, calle 29', 'B+', NULL, 'Caja Nacional', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(9, 1, 'Isabella', 'Nina', '9000004', '1978-01-15', 'F', '+591 64446076', 'isabella.nina@mail.test', 'Zona 4, calle 46', 'AB+', NULL, 'Particular', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(10, 1, 'Nicolas', 'Velasco', '9000005', '1970-03-28', 'M', '+591 65646012', 'nicolas.velasco@mail.test', 'Zona 5, calle 2', 'O-', NULL, 'Seguro Privado', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(11, 1, 'Camila', 'Ferrufino', '9000006', '1993-02-06', 'F', '+591 68314875', 'camila.ferrufino@mail.test', 'Zona 6, calle 13', 'A-', NULL, 'Caja Nacional', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(12, 1, 'Daniel', 'Paredes', '9000007', '2010-12-01', 'M', '+591 67393573', 'daniel.paredes@mail.test', 'Zona 7, calle 40', 'O+', NULL, 'Particular', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(13, 1, 'Antonella', 'Montano', '9000008', '2000-08-17', 'F', '+591 65026622', 'antonella.montano@mail.test', 'Zona 8, calle 3', 'A+', NULL, 'Seguro Privado', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(14, 1, 'Gabriel', 'Sandoval', '9000009', '1981-02-08', 'M', '+591 62816119', 'gabriel.sandoval@mail.test', 'Zona 9, calle 46', 'B+', NULL, 'Caja Nacional', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL),
	(15, 1, 'Renata', 'Ticona', '9000010', '1992-07-12', 'F', '+591 66308815', 'renata.ticona@mail.test', 'Zona 10, calle 22', 'AB+', NULL, 'Particular', 1, '2026-06-27 13:29:53', '2026-07-06 14:46:20', NULL);

-- Volcando estructura para tabla saas_clinica.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla saas_clinica.planes
CREATE TABLE IF NOT EXISTS `planes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_mensual` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_usuarios` int NOT NULL DEFAULT '5',
  `max_pacientes` int NOT NULL DEFAULT '500',
  `caracteristicas` text COLLATE utf8mb4_unicode_ci,
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.planes: ~2 rows (aproximadamente)
DELETE FROM `planes`;
INSERT INTO `planes` (`id`, `nombre`, `descripcion`, `precio_mensual`, `max_usuarios`, `max_pacientes`, `caracteristicas`, `destacado`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Basico', 'Para clinicas pequenas', 199.00, 5, 500, 'Agenda y pacientes\nFacturacion basica\nSoporte por email', 0, 1, '2026-06-15 06:22:19', '2026-06-15 06:22:19'),
	(2, 'Profesional', 'El mas elegido', 399.00, 20, 5000, 'Todo lo de Basico\nHistorias clinicas\nLaboratorio y recetas\nReportes avanzados', 1, 1, '2026-06-15 06:22:19', '2026-06-15 06:22:19'),
	(3, 'Premium', 'Para redes de clinicas', 799.00, 100, 50000, 'Todo lo de Profesional\nMultiples sedes\nSoporte prioritario 24/7\nRespaldos diarios', 0, 1, '2026-06-15 06:22:19', '2026-06-15 06:22:19');

-- Volcando estructura para tabla saas_clinica.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `unidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '0',
  `precio_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `vencimiento` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `productos_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `productos_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.productos: ~14 rows (aproximadamente)
DELETE FROM `productos`;
INSERT INTO `productos` (`id`, `clinica_id`, `nombre`, `codigo`, `categoria`, `descripcion`, `unidad`, `stock`, `stock_minimo`, `precio_compra`, `precio_venta`, `vencimiento`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'Paracetamol 500mg', 'MED-001', 'Analgesico', NULL, 'caja', 120, 20, 8.00, 15.00, '2027-06-15', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 'Amoxicilina 500mg', 'MED-002', 'Antibiotico', NULL, 'caja', 15, 20, 20.00, 35.00, '2027-06-15', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(3, 1, 'Guantes de latex', 'INS-001', 'Insumo', NULL, 'caja', 60, 10, 25.00, 40.00, '2027-06-15', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(4, 1, 'Jeringa 5ml', 'INS-002', 'Insumo', NULL, 'unidad', 400, 50, 1.20, 2.50, '2027-06-15', 1, '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(5, 1, 'Diclofenaco 50mg', 'PRD-101', 'Analgesico', NULL, 'caja', 80, 20, 6.00, 12.00, '2028-05-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(6, 1, 'Loratadina 10mg', 'PRD-102', 'Antialergico', NULL, 'caja', 45, 15, 7.00, 14.00, '2028-02-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(7, 1, 'Omeprazol 20mg', 'PRD-103', 'Gastrico', NULL, 'caja', 30, 20, 9.00, 18.00, '2027-11-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(8, 1, 'Suero fisiologico 500ml', 'PRD-104', 'Insumo', NULL, 'frasco', 120, 30, 4.00, 8.00, '2027-04-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(9, 1, 'Alcohol en gel 250ml', 'PRD-105', 'Insumo', NULL, 'frasco', 18, 25, 10.00, 20.00, '2027-07-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(10, 1, 'Vendas elasticas', 'PRD-106', 'Insumo', NULL, 'unidad', 200, 40, 3.00, 6.00, '2027-10-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(11, 1, 'Mascarillas N95', 'PRD-107', 'Insumo', NULL, 'caja', 12, 20, 30.00, 55.00, '2027-08-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(12, 1, 'Termometro digital', 'PRD-108', 'Equipo', NULL, 'unidad', 25, 10, 35.00, 60.00, '2028-04-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(13, 1, 'Gasas esteriles', 'PRD-109', 'Insumo', NULL, 'caja', 90, 20, 5.00, 10.00, '2028-04-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL),
	(14, 1, 'Ibuprofeno 400mg', 'PRD-110', 'Analgesico', NULL, 'caja', 60, 20, 8.00, 16.00, '2027-10-06', 1, '2026-06-27 13:29:55', '2026-07-06 14:46:22', NULL);

-- Volcando estructura para tabla saas_clinica.recetas
CREATE TABLE IF NOT EXISTS `recetas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `paciente_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `diagnostico` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recetas_paciente_id_foreign` (`paciente_id`),
  KEY `recetas_medico_id_foreign` (`medico_id`),
  KEY `recetas_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `recetas_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `recetas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `recetas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.recetas: ~21 rows (aproximadamente)
DELETE FROM `recetas`;
INSERT INTO `recetas` (`id`, `clinica_id`, `paciente_id`, `medico_id`, `fecha`, `diagnostico`, `notas`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 1, '2026-06-15', 'Hipertension arterial', 'Control en 30 dias. Dieta baja en sal.', '2026-06-15 06:22:21', '2026-06-15 06:22:21', NULL),
	(2, 1, 1, 1, '2025-11-18', 'Hipertension controlada', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(3, 1, 2, 2, '2025-12-18', 'Faringitis aguda', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(4, 1, 3, 3, '2026-01-07', 'Gastritis', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(5, 1, 4, 4, '2026-02-04', 'Migrana', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(6, 1, 5, 5, '2026-03-27', 'Dermatitis', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(7, 1, 6, 6, '2026-04-10', 'Lumbalgia', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(8, 1, 7, 7, '2026-05-15', 'Anemia leve', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(9, 1, 8, 8, '2026-06-12', 'Bronquitis', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(10, 1, 9, 9, '2025-11-11', 'Alergia estacional', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(11, 1, 10, 10, '2025-12-07', 'Control sano', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-06-27 13:29:55', '2026-06-27 13:29:55', NULL),
	(12, 1, 1, 1, '2025-12-16', 'Hipertension controlada', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(13, 1, 2, 2, '2026-01-07', 'Faringitis aguda', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(14, 1, 3, 3, '2026-02-05', 'Gastritis', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(15, 1, 4, 4, '2026-03-08', 'Migrana', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(16, 1, 5, 5, '2026-04-25', 'Dermatitis', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(17, 1, 6, 6, '2026-05-07', 'Lumbalgia', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(18, 1, 7, 7, '2026-06-13', 'Anemia leve', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(19, 1, 8, 8, '2026-07-26', 'Bronquitis', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(20, 1, 9, 9, '2025-12-02', 'Alergia estacional', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL),
	(21, 1, 10, 10, '2026-01-21', 'Control sano', 'Reposo e hidratacion. Control si persisten sintomas.', '2026-07-06 14:46:22', '2026-07-06 14:46:22', NULL);

-- Volcando estructura para tabla saas_clinica.receta_items
CREATE TABLE IF NOT EXISTS `receta_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `receta_id` bigint unsigned NOT NULL,
  `medicamento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duracion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indicaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receta_items_receta_id_foreign` (`receta_id`),
  CONSTRAINT `receta_items_receta_id_foreign` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.receta_items: ~42 rows (aproximadamente)
DELETE FROM `receta_items`;
INSERT INTO `receta_items` (`id`, `receta_id`, `medicamento`, `dosis`, `frecuencia`, `duracion`, `indicaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Enalapril 10mg', '1 tableta', 'Cada 12h', '30 dias', 'Via oral', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(2, 1, 'Aspirina 100mg', '1 tableta', 'Cada 24h', '30 dias', 'Despues del desayuno', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(3, 2, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(4, 2, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(5, 3, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(6, 3, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(7, 4, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(8, 4, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(9, 5, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(10, 5, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(11, 6, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(12, 6, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(13, 7, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(14, 7, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(15, 8, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(16, 8, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(17, 9, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(18, 9, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(19, 10, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(20, 10, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(21, 11, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Via oral', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(22, 11, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Despues de comidas', '2026-06-27 13:29:55', '2026-06-27 13:29:55'),
	(23, 12, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(24, 12, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(25, 13, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(26, 13, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(27, 14, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(28, 14, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(29, 15, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(30, 15, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(31, 16, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(32, 16, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(33, 17, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(34, 17, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(35, 18, 'Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(36, 18, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(37, 19, 'Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(38, 19, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(39, 20, 'Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(40, 20, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(41, 21, 'Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias', 'Via oral', '2026-07-06 14:46:22', '2026-07-06 14:46:22'),
	(42, 21, 'Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias', 'Despues de comidas', '2026-07-06 14:46:22', '2026-07-06 14:46:22');

-- Volcando estructura para tabla saas_clinica.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
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

-- Volcando datos para la tabla saas_clinica.sessions: ~17 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('8woVyGd3eX6vLrOA5LlxIQCJpepUTd3tKogf7lKp', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTUJFQ1Q1eWxReXZua1h3VHFGWU9lOTQ2Qm9DbVYybk1wUVZvVzdsUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785811465),
	('bink3fqD9nWcTqhgIkG8qFu1GdDEG2DjDHibRsS9', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoick9lRUh3TlcyeVlYWUhLeXB2QkdhdVBZSU9nSWJmd21ydm4xUXFHbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785686691),
	('FuTxveJfV1rbDTI72wm8M2SiY3UTfmIsW4QQWOzP', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUVlLcWlOcGlMeW1jcHhvekRpNW8zVXZDYTBFTEtQNzM3dndJbFlGdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785782061),
	('GCE1ue4D1E34X3tAqfFypi4oxy2owBGVGdw1Zdap', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUXNXY1NXMmF5cmR0QWtvT0RnMDFnT0tvQ3hXN01jN00zVGk1cEpmViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785765400),
	('grM9NI009Y3IgPkhGxG9Vt6fJYPlYFF2hWwcQRxu', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidlpRaUFRc001VjBtMVpYVFNNVjFVQzlLeE0ybjZWb2RvNW5NSGQ5ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785821470),
	('IW06zjfPmwSpHaTVYrGeNDt1BZ2Qv8CrLSSFPD4h', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieE1hQ0dOcnZlanVNT2lHSjRBczRRTnR6bHR6bm1nM3pBMHgxNGx5SCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785587625),
	('KbgNOBl1HBuQ4XVj9jd77b5aYV1ZsIT82dp7y2UP', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMU9zQkxtdDZWZlpZYXhISlM0MnNHMzhPaDNibzZCUXl0RllXUlZQRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785330328),
	('Lk46KdX2Tz2bFJypm9yPwLl2GU2U4WyuuiEyLfJs', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRVlyeDBuU0NLZzJpWVUxam9hMHBCekZTWDBsbTBCekwxaG96YjlrcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785944994),
	('LXogvsEmskMsMaWIU1KPcqVJQrQfXKM9gzvM87Lo', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTDNTalZDQXhsUFQwTmV2U0JQRWdCTmdMVUh5alRoSlc5ZXFiaXVQeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785462503),
	('MCaYXoSsDPADmckMZZcoqIKiuGXibfCFCNgXlQyi', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiemJPY0N3aEEwd3pQOTZrVE1CV0FTV3VvWW83Y3RtNzFPdHA4ZVRWVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785954557),
	('ORQpOu8zhqupBf8umybKpivhAN8RbR8lFORuEeRa', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTUViaVkxYkxjWmYxcGJSMWJCMHJ2R3VOSU5PNlc2dmhZSGhLRWN5ZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785553040),
	('ptOPqYeGuDZujddTCKmemqEeim3SsmD825dVDy33', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNnNlc2kwOW0xNHpadXozQ1NJdHo3M2xIUUhQeDM2T1VFU0JTZnlTNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785508075),
	('QBes3DCViptsKRtkKSwRT3AXO3EC6zzN4yIGgz8z', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUWh6QzZUNUVmcVdBcFhhWThrRGgzekh6ZGNkUEZiejdFWnRqdEtycCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785418018),
	('SeRdnkLBPLP32uXtaApK3emDV3Wyz5dnmFVaYvJK', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTU4xbm9YbGJROFBDY3NUU3pCV0JJVHlsN1FiNHBjc1lkdFBxMFd4WCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785884073),
	('SQQVjV3VH7i1iEnjNaVgBEMK5sKNmN6LxnNktW9v', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNE4xYVdmNm9kVWdkNnFlaWZjTDRPT2dPbkhXQllEdjlBZjFIWjhabyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1786032938),
	('uHTzrJZ98QolIZfWALeqlOg1t964SJxnPzpcptgJ', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOGJnTEM5aEFwOWdLNEZSdnFKancxOXcwNkpJaTNvYTlRbGFKRnlkaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785595521),
	('unsFF2TsP5rj0t2TFYtnzoFKFKbT2zTmY84t60yo', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib0k4SUdXeVZFWXpqWGkzUGNZQ3Z0S3o4dzJZMDY1MmN0OVpTVXIzZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4OC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1785855546);

-- Volcando estructura para tabla saas_clinica.suscripciones
CREATE TABLE IF NOT EXISTS `suscripciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `estado` enum('activa','vencida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ciclo` enum('mensual','anual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensual',
  `inicio` date NOT NULL,
  `fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suscripciones_clinica_id_foreign` (`clinica_id`),
  KEY `suscripciones_plan_id_foreign` (`plan_id`),
  CONSTRAINT `suscripciones_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `suscripciones_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.suscripciones: ~4 rows (aproximadamente)
DELETE FROM `suscripciones`;
INSERT INTO `suscripciones` (`id`, `clinica_id`, `plan_id`, `estado`, `monto`, `ciclo`, `inicio`, `fin`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, 'activa', 399.00, 'mensual', '2026-02-15', '2026-07-15', '2026-06-15 06:22:20', '2026-06-15 06:22:20'),
	(2, 2, 1, 'activa', 199.00, 'mensual', '2026-04-15', '2026-07-15', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(3, 3, 3, 'activa', 799.00, 'mensual', '2026-04-15', '2026-07-15', '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(4, 4, 1, 'vencida', 199.00, 'mensual', '2026-04-15', '2026-07-15', '2026-06-15 06:22:22', '2026-06-15 06:22:22');

-- Volcando estructura para tabla saas_clinica.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinica_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recepcion',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_clinica_id_foreign` (`clinica_id`),
  CONSTRAINT `users_clinica_id_foreign` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla saas_clinica.users: ~12 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `clinica_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'Super Administrador', 'superadmin@saas.test', '2026-06-15 06:22:20', '$2y$12$yDTPrwOV3/7fmodkTIxiNOwTKA/qk1.VytUv2hW2EMwpkvtPCroQa', 'super_admin', NULL, NULL, 1, NULL, '2026-06-15 06:22:20', '2026-06-15 06:22:20'),
	(2, 1, 'Administrador General', 'admin@clinica.test', '2026-06-15 06:22:20', '$2y$12$iqnvwHB4FtIP6GPyYRrKvusvCi9SKaNZNhPF.KJ0bRNN9MXpG6yeO', 'admin', '+591 70000000', NULL, 1, NULL, '2026-06-15 06:22:20', '2026-06-15 06:22:20'),
	(3, 1, 'Dra. Mariana Lopez', 'medico@clinica.test', '2026-06-15 06:22:20', '$2y$12$jAm1Dbe.kbxOjQoUwMTdNe3eMPZ4yROW8UNuJAu/X.ZjdRrxOQmHC', 'medico', '+591 71111111', NULL, 1, NULL, '2026-06-15 06:22:20', '2026-06-15 06:22:20'),
	(4, 1, 'Recepcion Clinica', 'recepcion@clinica.test', '2026-06-15 06:22:21', '$2y$12$nARhxm1xErp.vviFV.bHMOlg5dUQCKxysfXfPpuQkKwc6ijpWLBmK', 'recepcion', '+591 72222222', NULL, 1, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(5, 2, 'Admin Clinica San Rafael', 'admin@sanrafael.test', '2026-06-15 06:22:21', '$2y$12$Hy8xDTLAF9oAnxBQDBZ3dOMjL/qmGa5i64HHu8zxmJqlkpHbe3jJK', 'admin', NULL, NULL, 1, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(6, 3, 'Admin Centro Medico Vida', 'admin@vida.test', '2026-06-15 06:22:21', '$2y$12$AJcawgePgVBTUP58S12sZu1VNfrKiy21SGHYCyCpUsFnl2PSnT9jO', 'admin', NULL, NULL, 1, NULL, '2026-06-15 06:22:21', '2026-06-15 06:22:21'),
	(7, 4, 'Admin Policlinico Norte', 'admin@norte.test', '2026-06-15 06:22:22', '$2y$12$lgbIUiFMTYWt62ooJaA2yek97pvUkS5uhoz/dI27OcCCvLjPax6KK', 'admin', NULL, NULL, 1, NULL, '2026-06-15 06:22:22', '2026-06-15 06:22:22'),
	(8, 1, 'Dra. Sofia Quispe', 'squispe@clinica.test', '2026-07-06 14:46:21', '$2y$12$jsXoDjxe4q3XjbeBFEyCYeFqyFXemDdQugA01/uMG0XOw1u82I9oS', 'medico', '+591 79208266', NULL, 1, NULL, '2026-06-27 13:29:53', '2026-07-06 14:46:21'),
	(9, 1, 'Dr. Andres Rojas', 'arojas@clinica.test', '2026-07-06 14:46:21', '$2y$12$lMD8T.99NEx8jYDKML/lJ.HqMbhfsQis3yEaSCVg4/vlvhIDNneFm', 'medico', '+591 75827211', NULL, 1, NULL, '2026-06-27 13:29:54', '2026-07-06 14:46:21'),
	(10, 1, 'Recepcion Tarde', 'recepcion2@clinica.test', '2026-07-06 14:46:21', '$2y$12$IOOG.7YUgyWBwMZ6Z0Hhw.cukiZHzJ.sb/spDiBrBumaf1ixfdRYe', 'recepcion', '+591 74034519', NULL, 1, NULL, '2026-06-27 13:29:54', '2026-07-06 14:46:21'),
	(11, 1, 'Caja Facturacion', 'caja@clinica.test', '2026-07-06 14:46:21', '$2y$12$XnfBLx5DQIiUk.7aEE48VOlI2hxPOf6AuVkNUW.aHHGoIuyL/Mq3.', 'recepcion', '+591 71991990', NULL, 1, NULL, '2026-06-27 13:29:54', '2026-07-06 14:46:21'),
	(12, 1, 'Asistente Admin', 'asistente@clinica.test', '2026-07-06 14:46:22', '$2y$12$x5PBHBzEhdcjLU4jkU.fNew2qZdmQwdOrBIh3ieNYzuXYz2WQRUhu', 'admin', '+591 77335216', NULL, 1, NULL, '2026-06-27 13:29:54', '2026-07-06 14:46:22');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
