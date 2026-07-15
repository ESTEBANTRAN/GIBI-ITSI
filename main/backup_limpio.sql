-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: bienestar_estudiantil_db
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
-- Table structure for table `becas`
--

DROP TABLE IF EXISTS `becas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `becas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `requisitos` text DEFAULT NULL COMMENT 'Descripci?n de los requisitos generales',
  `puntaje_minimo_requerido` decimal(5,2) DEFAULT NULL COMMENT 'Ejemplo de requisito verificable por el sistema',
  `activa` tinyint(1) DEFAULT 1,
  `nombre_beca` varchar(255) GENERATED ALWAYS AS (`nombre`) STORED,
  `documentos_requisitos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Lista de documentos requeridos para la beca' CHECK (json_valid(`documentos_requisitos`)),
  `periodo_vigente_id` int(10) unsigned DEFAULT NULL COMMENT 'ID del per??odo acad??mico vigente para la beca',
  `fecha_inicio_vigencia` date DEFAULT NULL COMMENT 'Fecha de inicio de vigencia de la beca',
  `fecha_fin_vigencia` date DEFAULT NULL COMMENT 'Fecha de fin de vigencia de la beca',
  `monto_beca` decimal(10,2) DEFAULT NULL COMMENT 'Monto de la beca',
  `tipo_beca` enum('Académica','Económica','Deportiva','Cultural','Investigación','Otros') NOT NULL DEFAULT 'Académica',
  `cupos_disponibles` int(10) unsigned DEFAULT NULL COMMENT 'N??mero de cupos disponibles',
  `estado` enum('Activa','Inactiva','Cerrada') NOT NULL DEFAULT 'Activa' COMMENT 'Estado de la beca',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creaci??n de la beca',
  `creado_por` int(10) unsigned DEFAULT NULL COMMENT 'ID del administrador que cre?? la beca',
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `actualizado_por` int(10) unsigned DEFAULT NULL,
  `prioridad` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_becas_creado_por` (`creado_por`),
  KEY `idx_becas_periodo_estado` (`periodo_vigente_id`,`estado`),
  KEY `idx_becas_tipo_activa` (`tipo_beca`,`activa`),
  CONSTRAINT `fk_becas_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_becas_periodo` FOREIGN KEY (`periodo_vigente_id`) REFERENCES `periodos_academicos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `becas_documentos_requisitos`
--

DROP TABLE IF EXISTS `becas_documentos_requisitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `becas_documentos_requisitos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `beca_id` int(10) unsigned NOT NULL COMMENT 'ID de la beca',
  `nombre_documento` varchar(255) NOT NULL COMMENT 'Nombre del documento requerido',
  `descripcion` text DEFAULT NULL COMMENT 'Descripci??n del documento',
  `tipo_documento` varchar(100) NOT NULL COMMENT 'Tipo de documento (PDF, IMG, etc.)',
  `obligatorio` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Si el documento es obligatorio',
  `orden_verificacion` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Orden en que se debe verificar',
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COMMENT 'Estado del requisito',
  PRIMARY KEY (`id`),
  KEY `idx_beca_orden` (`beca_id`,`orden_verificacion`),
  CONSTRAINT `fk_requisito_beca` FOREIGN KEY (`beca_id`) REFERENCES `becas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Documentos requisitos para cada beca';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `carreras`
--

DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carreras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `semestres` int(2) NOT NULL DEFAULT 5,
  `activa` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorias_evaluacion`
--

DROP TABLE IF EXISTS `categorias_evaluacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_evaluacion` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `peso` decimal(3,2) DEFAULT 1.00,
  `estado` enum('Activa','Inactiva') DEFAULT 'Activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorias_solicitud_ayuda`
--

DROP TABLE IF EXISTS `categorias_solicitud_ayuda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_solicitud_ayuda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `icono` varchar(50) DEFAULT 'bi-question-circle',
  `activo` tinyint(1) DEFAULT 1,
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned DEFAULT NULL,
  `estudiante_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `lugar_o_enlace` varchar(255) NOT NULL,
  `estado` enum('Programada','Completada','Cancelada') NOT NULL DEFAULT 'Programada',
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `competencias`
--

DROP TABLE IF EXISTS `competencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competencias` (
  `id_competencia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` enum('Técnica','Soft Skills','Liderazgo','Gestión') DEFAULT NULL,
  `nivel_requerido` enum('Básico','Intermedio','Avanzado','Experto') DEFAULT NULL,
  `estado` enum('Activa','Inactiva') DEFAULT 'Activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_competencia`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuracion_sistema`
--

DROP TABLE IF EXISTS `configuracion_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion_sistema` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` varchar(50) DEFAULT 'text',
  `categoria` varchar(50) DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documentos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL COMMENT 'El usuario que sube el documento',
  `ficha_id` int(10) unsigned DEFAULT NULL COMMENT 'Asociado a una ficha socioecon?mica',
  `solicitud_beca_id` int(10) unsigned DEFAULT NULL COMMENT 'Asociado a una solicitud de beca',
  `tipo_documento` varchar(255) DEFAULT NULL COMMENT 'Describe el requisito. Ej: C?dula, Certificado de Notas.',
  `nombre_archivo` varchar(255) NOT NULL,
  `path_archivo` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) NOT NULL,
  `tamano_kb` int(10) unsigned NOT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `ficha_id` (`ficha_id`),
  KEY `solicitud_beca_id` (`solicitud_beca_id`),
  CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentos_ibfk_2` FOREIGN KEY (`ficha_id`) REFERENCES `fichas_socioeconomicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentos_ibfk_3` FOREIGN KEY (`solicitud_beca_id`) REFERENCES `solicitudes_becas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=610 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documentos_solicitud_becas`
--

DROP TABLE IF EXISTS `documentos_solicitud_becas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documentos_solicitud_becas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_beca_id` int(10) unsigned NOT NULL,
  `documento_requerido_id` int(10) unsigned NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `orden_revision` int(10) unsigned NOT NULL DEFAULT 1,
  `estado` enum('Pendiente','En Revision','Aprobado','Rechazado') NOT NULL DEFAULT 'Pendiente',
  `observaciones` text DEFAULT NULL,
  `revisado_por` int(10) unsigned DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `tamano_archivo` int(10) unsigned DEFAULT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `google_drive_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_beca` (`solicitud_beca_id`),
  KEY `idx_documento_requerido` (`documento_requerido_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_orden_revision` (`orden_revision`),
  KEY `idx_revisado_por` (`revisado_por`),
  KEY `idx_documentos_solicitud_beca_id` (`solicitud_beca_id`),
  KEY `idx_documentos_estado` (`estado`),
  CONSTRAINT `fk_doc_requerido` FOREIGN KEY (`documento_requerido_id`) REFERENCES `becas_documentos_requisitos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_revisado_por` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_doc_solicitud_beca` FOREIGN KEY (`solicitud_beca_id`) REFERENCES `solicitudes_becas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_actualizar_documentos_revisados` AFTER UPDATE ON `documentos_solicitud_becas` FOR EACH ROW BEGIN
    DECLARE docs_aprobados INT DEFAULT 0;
    DECLARE total_docs INT DEFAULT 0;
    
    IF NEW.estado = 'Aprobado' AND OLD.estado != 'Aprobado' THEN
        SELECT COUNT(*) INTO docs_aprobados 
        FROM `documentos_solicitud_becas` 
        WHERE `solicitud_beca_id` = NEW.solicitud_beca_id AND `estado` = 'Aprobado';
        
        SELECT COUNT(*) INTO total_docs 
        FROM `documentos_solicitud_becas` 
        WHERE `solicitud_beca_id` = NEW.solicitud_beca_id;
        
        UPDATE `solicitudes_becas` 
        SET `documentos_revisados` = docs_aprobados,
            `total_documentos` = total_docs,
            `documento_actual_revision` = LEAST(docs_aprobados + 1, total_docs)
        WHERE `id` = NEW.solicitud_beca_id;
        
        
        IF docs_aprobados = total_docs THEN
            UPDATE `solicitudes_becas` 
            SET `estado` = 'Documentos Aprobados'
            WHERE `id` = NEW.solicitud_beca_id;
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `estudiantes_habilitacion_becas`
--

DROP TABLE IF EXISTS `estudiantes_habilitacion_becas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudiantes_habilitacion_becas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(10) unsigned NOT NULL,
  `periodo_id` int(10) unsigned NOT NULL,
  `ficha_completada` tinyint(1) NOT NULL DEFAULT 0,
  `puede_solicitar_becas` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_habilitacion` timestamp NULL DEFAULT NULL,
  `habilitado_por` int(10) unsigned DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_estudiante_periodo` (`estudiante_id`,`periodo_id`),
  KEY `idx_puede_solicitar` (`puede_solicitar_becas`),
  KEY `idx_ficha_completada` (`ficha_completada`),
  KEY `idx_habilitado_por` (`habilitado_por`),
  KEY `fk_hab_periodo` (`periodo_id`),
  CONSTRAINT `fk_hab_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hab_habilitado_por` FOREIGN KEY (`habilitado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_hab_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_academicos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fichas_becas_relacion`
--

DROP TABLE IF EXISTS `fichas_becas_relacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fichas_becas_relacion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ficha_id` int(10) unsigned NOT NULL,
  `solicitud_beca_id` int(10) unsigned DEFAULT NULL,
  `tipo_relacion` enum('Solicitando','Becado','Sin_Beca') NOT NULL DEFAULT 'Sin_Beca',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ficha_id` (`ficha_id`),
  KEY `solicitud_beca_id` (`solicitud_beca_id`),
  KEY `idx_fichas_tipo_relacion` (`tipo_relacion`)
) ENGINE=InnoDB AUTO_INCREMENT=613 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fichas_socioeconomicas`
--

DROP TABLE IF EXISTS `fichas_socioeconomicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fichas_socioeconomicas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(10) unsigned NOT NULL,
  `periodo_id` int(10) unsigned NOT NULL,
  `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo flexible para almacenar toda la data de la ficha en formato JSON' CHECK (json_valid(`json_data`)),
  `estado` enum('Borrador','Enviada','Revisada','Aprobada','Rechazada') NOT NULL DEFAULT 'Borrador',
  `revisada_por_admin` tinyint(1) DEFAULT 0,
  `fecha_revision_admin` datetime DEFAULT NULL,
  `observaciones_admin` text DEFAULT NULL COMMENT 'Comentario obligatorio cuando se rechaza una ficha',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_revision` datetime DEFAULT NULL,
  `revisado_por` int(10) unsigned DEFAULT NULL COMMENT 'ID del admin que revis? la ficha',
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `actualizado_por` int(10) unsigned DEFAULT NULL,
  `puntaje_calculado` decimal(5,2) DEFAULT NULL,
  `relacionada_beca` tinyint(1) DEFAULT 0 COMMENT 'Indica si la ficha est? relacionada con una solicitud de beca',
  `fecha_relacion_beca` datetime DEFAULT NULL COMMENT 'Fecha cuando se relacion? con beca',
  PRIMARY KEY (`id`),
  UNIQUE KEY `estudiante_id` (`estudiante_id`,`periodo_id`),
  KEY `idx_fichas_estado` (`estado`),
  KEY `idx_fichas_periodo` (`periodo_id`),
  KEY `idx_fichas_estudiante` (`estudiante_id`),
  KEY `idx_fichas_estado_periodo` (`estado`,`periodo_id`),
  KEY `idx_fichas_estudiante_periodo` (`estudiante_id`,`periodo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2469 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `validar_comentario_rechazo` BEFORE UPDATE ON `fichas_socioeconomicas` FOR EACH ROW BEGIN
    IF NEW.estado = 'Rechazada' AND (NEW.observaciones_admin IS NULL OR TRIM(NEW.observaciones_admin) = '') THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Es obligatorio ingresar un comentario cuando se rechaza una ficha';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_ficha_completada_habilitar_becas` AFTER UPDATE ON `fichas_socioeconomicas` FOR EACH ROW BEGIN
    IF NEW.estado = 'Aprobada' AND OLD.estado != 'Aprobada' THEN
        INSERT INTO `estudiantes_habilitacion_becas` 
        (`estudiante_id`, `periodo_id`, `ficha_completada`, `puede_solicitar_becas`, `fecha_habilitacion`, `habilitado_por`)
        VALUES 
        (NEW.estudiante_id, NEW.periodo_id, 1, 1, NOW(), NEW.revisado_por)
        ON DUPLICATE KEY UPDATE 
        `ficha_completada` = 1,
        `puede_solicitar_becas` = 1,
        `fecha_habilitacion` = NOW(),
        `habilitado_por` = NEW.revisado_por,
        `updated_at` = NOW();
    END IF;
    
    IF NEW.estado = 'Rechazada' AND OLD.estado != 'Rechazada' THEN
        UPDATE `estudiantes_habilitacion_becas` 
        SET `puede_solicitar_becas` = 0, `updated_at` = NOW()
        WHERE `estudiante_id` = NEW.estudiante_id AND `periodo_id` = NEW.periodo_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `flujo_aprobacion_documentos`
--

DROP TABLE IF EXISTS `flujo_aprobacion_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flujo_aprobacion_documentos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_beca_id` int(10) unsigned NOT NULL,
  `documento_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `accion` enum('Aprobar','Rechazar','Devolver','Observar') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_origen` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_flujo` (`solicitud_beca_id`),
  KEY `idx_documento_flujo` (`documento_id`),
  KEY `idx_admin_flujo` (`admin_id`),
  KEY `idx_fecha_accion` (`fecha_accion`),
  CONSTRAINT `fk_flujo_admin` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_flujo_documento` FOREIGN KEY (`documento_id`) REFERENCES `documentos_solicitud_becas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_flujo_solicitud` FOREIGN KEY (`solicitud_beca_id`) REFERENCES `solicitudes_becas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=610 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historial_estados_becas`
--

DROP TABLE IF EXISTS `historial_estados_becas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_estados_becas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_beca_id` int(10) unsigned NOT NULL COMMENT 'ID de la solicitud de beca',
  `estado_anterior` enum('Postulada','En Revisión','Aprobada','Rechazada','Lista de Espera') DEFAULT NULL,
  `estado_nuevo` enum('Postulada','En Revisión','Aprobada','Rechazada','Lista de Espera') DEFAULT NULL,
  `motivo_cambio` text DEFAULT NULL COMMENT 'Motivo del cambio de estado',
  `cambiado_por` int(10) unsigned NOT NULL COMMENT 'ID del administrador que realiz?? el cambio',
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha del cambio de estado',
  `observaciones` text DEFAULT NULL COMMENT 'Observaciones adicionales del cambio',
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_fecha` (`solicitud_beca_id`,`fecha_cambio`),
  KEY `idx_cambiado_por` (`cambiado_por`),
  CONSTRAINT `fk_historial_cambiado_por` FOREIGN KEY (`cambiado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_historial_solicitud` FOREIGN KEY (`solicitud_beca_id`) REFERENCES `solicitudes_becas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1259 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Historial de cambios de estado de solicitudes de becas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accion` varchar(100) NOT NULL,
  `tabla` varchar(100) NOT NULL,
  `registro_id` varchar(50) DEFAULT NULL,
  `datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos`)),
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notificaciones_becas`
--

DROP TABLE IF EXISTS `notificaciones_becas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones_becas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL COMMENT 'ID del usuario que recibe la notificaci??n',
  `solicitud_beca_id` int(10) unsigned DEFAULT NULL COMMENT 'ID de la solicitud de beca relacionada',
  `tipo_notificacion` enum('Solicitud_Enviada','Documento_Aprobado','Documento_Rechazado','Beca_Aprobada','Beca_Rechazada','Documento_Pendiente') NOT NULL COMMENT 'Tipo de notificaci??n',
  `titulo` varchar(255) NOT NULL COMMENT 'T??tulo de la notificaci??n',
  `mensaje` text NOT NULL COMMENT 'Mensaje de la notificaci??n',
  `leida` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Si la notificaci??n ha sido le??da',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creaci??n de la notificaci??n',
  `fecha_lectura` timestamp NULL DEFAULT NULL COMMENT 'Fecha en que se ley?? la notificaci??n',
  PRIMARY KEY (`id`),
  KEY `idx_usuario_leida` (`usuario_id`,`leida`),
  KEY `idx_tipo_fecha` (`tipo_notificacion`,`fecha_creacion`),
  KEY `fk_notificacion_solicitud` (`solicitud_beca_id`),
  KEY `idx_notificaciones_usuario_tipo` (`usuario_id`,`tipo_notificacion`),
  CONSTRAINT `fk_notificacion_solicitud` FOREIGN KEY (`solicitud_beca_id`) REFERENCES `solicitudes_becas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notificacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Notificaciones relacionadas con becas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `observaciones_fichas`
--

DROP TABLE IF EXISTS `observaciones_fichas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `observaciones_fichas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ficha_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `observacion` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ficha_id` (`ficha_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  CONSTRAINT `observaciones_fichas_ibfk_1` FOREIGN KEY (`ficha_id`) REFERENCES `fichas_socioeconomicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `observaciones_fichas_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Observaciones sobre fichas socioecon?micas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pdf_codigos_verificacion`
--

DROP TABLE IF EXISTS `pdf_codigos_verificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pdf_codigos_verificacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `tipo_documento` varchar(100) NOT NULL,
  `id_documento` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  `ip_generacion` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `estado` enum('activo','verificado','expirado') DEFAULT 'activo',
  `fecha_verificacion` datetime DEFAULT NULL,
  `ip_verificacion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_tipo_documento` (`tipo_documento`),
  KEY `idx_fecha_generacion` (`fecha_generacion`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `periodos_academicos`
--

DROP TABLE IF EXISTS `periodos_academicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periodos_academicos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL COMMENT 'Ej: 2025-2026 Semestre I',
  `estado` varchar(20) DEFAULT 'Activo',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo_fichas` tinyint(1) DEFAULT 0 COMMENT 'Indica si el per?odo est? activo para subir fichas',
  `activo_becas` tinyint(1) DEFAULT 0 COMMENT 'Indica si el per?odo est? activo para solicitar becas',
  `activo` tinyint(1) DEFAULT 1 COMMENT 'Campo de compatibilidad para mantener activo el per?odo',
  `vigente_estudiantes` tinyint(1) DEFAULT 0 COMMENT 'Indica si el per?odo es visible para estudiantes',
  `limite_fichas` int(10) unsigned DEFAULT NULL COMMENT 'L?mite de fichas socioecon?micas para este per?odo',
  `limite_becas` int(10) unsigned DEFAULT NULL COMMENT 'L?mite de becas para este per?odo',
  `fichas_creadas` int(10) unsigned DEFAULT 0,
  `becas_asignadas` int(10) unsigned DEFAULT 0,
  `descripcion` text DEFAULT NULL COMMENT 'Descripci?n adicional del per?odo',
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_periodos_activo` (`activo`),
  KEY `idx_periodos_fechas` (`fecha_inicio`,`fecha_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `respaldos`
--

DROP TABLE IF EXISTS `respaldos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respaldos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `tamano_bytes` bigint(20) NOT NULL,
  `tipo` varchar(50) DEFAULT 'manual',
  `estado` varchar(50) DEFAULT 'completado',
  `descripcion` text DEFAULT NULL,
  `creado_por` int(10) unsigned DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `respuestas_predefinidas`
--

DROP TABLE IF EXISTS `respuestas_predefinidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respuestas_predefinidas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `contenido` text NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `publica` tinyint(1) DEFAULT 1,
  `activa` tinyint(1) DEFAULT 1,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `respuestas_solicitudes_ayuda`
--

DROP TABLE IF EXISTS `respuestas_solicitudes_ayuda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respuestas_solicitudes_ayuda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_ayuda_id` int(11) NOT NULL,
  `respuesta` text NOT NULL,
  `fecha_respuesta` datetime DEFAULT current_timestamp(),
  `id_responsable` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `permisos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permisos`)),
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitudes_ayuda`
--

DROP TABLE IF EXISTS `solicitudes_ayuda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_ayuda` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(10) unsigned NOT NULL COMMENT 'ID del estudiante que solicita ayuda',
  `asunto` varchar(255) NOT NULL COMMENT 'Breve descripci?n del motivo de la solicitud',
  `categoria_id` int(11) DEFAULT NULL,
  `asunto_personalizado` text DEFAULT NULL,
  `descripcion` text NOT NULL COMMENT 'Descripci?n detallada de la solicitud de ayuda',
  `comentarios_resolucion` text DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Pendiente','En Proceso','Resuelta','Cerrada') NOT NULL DEFAULT 'Pendiente' COMMENT 'Estado actual de la solicitud',
  `prioridad` enum('Baja','Media','Alta','Urgente') NOT NULL DEFAULT 'Media' COMMENT 'Prioridad de la solicitud',
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_responsable` int(10) unsigned DEFAULT NULL COMMENT 'ID del administrativo encargado de la solicitud',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_solicitud_estudiante` (`id_estudiante`),
  KEY `fk_solicitud_responsable` (`id_responsable`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `solicitudes_ayuda_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_solicitud_ayuda` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitudes_ayuda_mejorada`
--

DROP TABLE IF EXISTS `solicitudes_ayuda_mejorada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_ayuda_mejorada` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(10) unsigned NOT NULL,
  `tipo_solicitud` enum('Beca','Ficha','Documentos','General','Tecnico') NOT NULL DEFAULT 'General',
  `prioridad` enum('Baja','Media','Alta','Urgente') NOT NULL DEFAULT 'Media',
  `asunto` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `estado` enum('Abierta','En Proceso','Resuelta','Cerrada') NOT NULL DEFAULT 'Abierta',
  `asignado_a` int(10) unsigned DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_asignacion` timestamp NULL DEFAULT NULL,
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `tiempo_respuesta_hrs` int(10) unsigned DEFAULT NULL,
  `satisfaccion_usuario` enum('1','2','3','4','5') DEFAULT NULL,
  `comentarios_resolucion` text DEFAULT NULL,
  `archivos_adjuntos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`archivos_adjuntos`)),
  PRIMARY KEY (`id`),
  KEY `idx_estudiante_ayuda` (`estudiante_id`),
  KEY `idx_tipo_solicitud` (`tipo_solicitud`),
  KEY `idx_estado_ayuda` (`estado`),
  KEY `idx_prioridad` (`prioridad`),
  KEY `idx_asignado_a` (`asignado_a`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  CONSTRAINT `fk_ayuda_asignado` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ayuda_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitudes_becas`
--

DROP TABLE IF EXISTS `solicitudes_becas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_becas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(10) unsigned NOT NULL,
  `beca_id` int(10) unsigned NOT NULL,
  `periodo_id` int(10) unsigned NOT NULL,
  `estado` enum('Postulada','En Revisión','Aprobada','Rechazada','Lista de Espera') NOT NULL DEFAULT 'Postulada',
  `observaciones` text DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_revision` timestamp NULL DEFAULT NULL COMMENT 'Fecha de revisi??n de la solicitud',
  `revisado_por` int(10) unsigned DEFAULT NULL COMMENT 'ID del administrador que revis?? la solicitud',
  `motivo_rechazo` text DEFAULT NULL COMMENT 'Motivo del rechazo si es rechazada',
  `documentos_revisados` int(10) unsigned NOT NULL DEFAULT 0,
  `total_documentos` int(10) unsigned NOT NULL DEFAULT 0,
  `documento_actual_revision` int(10) unsigned DEFAULT 1,
  `puede_solicitar_beca` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL COMMENT 'Fecha de aprobaci??n de la beca',
  `fecha_rechazo` timestamp NULL DEFAULT NULL COMMENT 'Fecha de rechazo de la beca',
  `porcentaje_avance` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de avance en la verificaci??n de documentos',
  `documento_actual_verificando` int(10) unsigned DEFAULT NULL COMMENT 'ID del documento actual en verificaci??n',
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `actualizado_por` int(10) unsigned DEFAULT NULL,
  `observaciones_admin` text DEFAULT NULL,
  `aprobado_por` int(10) unsigned DEFAULT NULL,
  `rechazado_por` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `beca_id` (`beca_id`),
  KEY `periodo_id` (`periodo_id`),
  KEY `fk_solicitud_revisado_por` (`revisado_por`),
  KEY `idx_solicitudes_estudiante_periodo` (`estudiante_id`,`periodo_id`),
  KEY `idx_solicitudes_estado_fecha` (`estado`,`fecha_solicitud`),
  KEY `idx_solicitudes_becas_estudiante_periodo` (`estudiante_id`,`periodo_id`),
  KEY `idx_solicitudes_becas_estado` (`estado`),
  KEY `idx_solicitudes_becas_fecha` (`fecha_solicitud`),
  CONSTRAINT `fk_solicitud_revisado_por` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=352 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitudes_becas_documentos`
--

DROP TABLE IF EXISTS `solicitudes_becas_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_becas_documentos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_beca_id` int(10) unsigned NOT NULL COMMENT 'ID de la solicitud de beca',
  `documento_requisito_id` int(10) unsigned NOT NULL COMMENT 'ID del documento requisito',
  `documento_subido_id` int(10) unsigned DEFAULT NULL COMMENT 'ID del documento subido por el estudiante',
  `estado` enum('Pendiente','En Revisión','Aprobado','Rechazado') NOT NULL DEFAULT 'Pendiente',
  `fecha_revision` timestamp NULL DEFAULT NULL COMMENT 'Fecha de revisi??n del documento',
  `revisado_por` int(10) unsigned DEFAULT NULL COMMENT 'ID del administrador que revis?? el documento',
  `observaciones` text DEFAULT NULL COMMENT 'Observaciones del administrador sobre el documento',
  `motivo_rechazo` text DEFAULT NULL COMMENT 'Motivo del rechazo si es rechazado',
  `orden_verificacion` int(10) unsigned NOT NULL COMMENT 'Orden de verificaci??n del documento',
  `fecha_aprobacion` timestamp NULL DEFAULT NULL COMMENT 'Fecha de aprobaci??n del documento',
  `fecha_rechazo` timestamp NULL DEFAULT NULL COMMENT 'Fecha de rechazo del documento',
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_orden` (`solicitud_beca_id`,`orden_verificacion`),
  KEY `idx_estado_documento` (`estado`,`documento_requisito_id`),
  KEY `fk_solicitud_doc_requisito` (`documento_requisito_id`),
  KEY `fk_solicitud_doc_subido` (`documento_subido_id`),
  KEY `fk_solicitud_doc_revisado_por` (`revisado_por`),
  KEY `idx_documentos_solicitud_orden` (`solicitud_beca_id`,`orden_verificacion`),
  CONSTRAINT `fk_solicitud_doc_requisito` FOREIGN KEY (`documento_requisito_id`) REFERENCES `becas_documentos_requisitos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_solicitud_doc_revisado_por` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_solicitud_doc_solicitud` FOREIGN KEY (`solicitud_beca_id`) REFERENCES `solicitudes_becas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_solicitud_doc_subido` FOREIGN KEY (`documento_subido_id`) REFERENCES `documentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4450 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Seguimiento de documentos de solicitudes de becas';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `actualizar_porcentaje_avance_beca` AFTER UPDATE ON `solicitudes_becas_documentos` FOR EACH ROW BEGIN
    DECLARE total_docs INT;
    DECLARE docs_aprobados INT;
    DECLARE nuevo_porcentaje DECIMAL(5,2);
    
    
    SELECT COUNT(*) INTO total_docs
    FROM solicitudes_becas_documentos 
    WHERE solicitud_beca_id = NEW.solicitud_beca_id;
    
    
    SELECT COUNT(*) INTO docs_aprobados
    FROM solicitudes_becas_documentos 
    WHERE solicitud_beca_id = NEW.solicitud_beca_id AND estado = 'Aprobado';
    
    
    SET nuevo_porcentaje = (docs_aprobados / total_docs) * 100;
    
    
    UPDATE solicitudes_becas 
    SET porcentaje_avance = nuevo_porcentaje
    WHERE id = NEW.solicitud_beca_id;
    
    
    IF docs_aprobados = total_docs THEN
        UPDATE solicitudes_becas 
        SET estado = 'En Revisi??n', 
            fecha_revision = CURRENT_TIMESTAMP
        WHERE id = NEW.solicitud_beca_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rol_id` int(10) unsigned NOT NULL,
  `carrera_id` int(10) unsigned DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `carrera` varchar(100) DEFAULT NULL,
  `semestre` varchar(50) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo','Suspendido') DEFAULT 'Activo',
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `intentos_fallidos` int(10) unsigned NOT NULL DEFAULT 0,
  `bloqueado_hasta` timestamp NULL DEFAULT NULL,
  `configuraciones_usuario` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuraciones_usuario`)),
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula` (`cedula`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  KEY `idx_usuarios_carrera` (`carrera_id`),
  KEY `idx_usuarios_nombre_apellido` (`nombre`,`apellido`),
  KEY `idx_usuarios_cedula` (`cedula`),
  KEY `idx_usuarios_estado` (`estado`),
  KEY `idx_usuarios_ultimo_acceso` (`ultimo_acceso`),
  KEY `idx_usuarios_fecha_registro` (`fecha_registro`),
  KEY `idx_intentos_fallidos` (`intentos_fallidos`),
  KEY `idx_bloqueado_hasta` (`bloqueado_hasta`)
) ENGINE=InnoDB AUTO_INCREMENT=652 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v_becas_completas`
--

DROP TABLE IF EXISTS `v_becas_completas`;
/*!50001 DROP VIEW IF EXISTS `v_becas_completas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_becas_completas` AS SELECT
 1 AS `id`,
  1 AS `nombre`,
  1 AS `descripcion`,
  1 AS `tipo_beca`,
  1 AS `monto_beca`,
  1 AS `cupos_disponibles`,
  1 AS `estado`,
  1 AS `activa`,
  1 AS `periodo_vigente`,
  1 AS `fecha_inicio_vigencia`,
  1 AS `fecha_fin_vigencia`,
  1 AS `creado_por`,
  1 AS `fecha_creacion`,
  1 AS `total_solicitudes`,
  1 AS `solicitudes_aprobadas`,
  1 AS `solicitudes_rechazadas`,
  1 AS `solicitudes_en_revision`,
  1 AS `solicitudes_lista_espera`,
  1 AS `solicitudes_postuladas` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_dashboard_admin_bienestar`
--

DROP TABLE IF EXISTS `v_dashboard_admin_bienestar`;
/*!50001 DROP VIEW IF EXISTS `v_dashboard_admin_bienestar`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_dashboard_admin_bienestar` AS SELECT
 1 AS `periodo_id`,
  1 AS `periodo_nombre`,
  1 AS `fecha_inicio`,
  1 AS `fecha_fin`,
  1 AS `limite_fichas`,
  1 AS `limite_becas`,
  1 AS `fichas_creadas`,
  1 AS `becas_asignadas`,
  1 AS `fichas_disponibles`,
  1 AS `becas_disponibles`,
  1 AS `estado_fichas`,
  1 AS `estado_becas` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_estadisticas_sistema`
--

DROP TABLE IF EXISTS `v_estadisticas_sistema`;
/*!50001 DROP VIEW IF EXISTS `v_estadisticas_sistema`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_estadisticas_sistema` AS SELECT
 1 AS `total_estudiantes`,
  1 AS `total_admin_bienestar`,
  1 AS `total_superadmin`,
  1 AS `periodos_activos`,
  1 AS `fichas_completadas`,
  1 AS `becas_pendientes`,
  1 AS `ayudas_pendientes` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_fichas_admin`
--

DROP TABLE IF EXISTS `v_fichas_admin`;
/*!50001 DROP VIEW IF EXISTS `v_fichas_admin`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_fichas_admin` AS SELECT
 1 AS `id`,
  1 AS `estado`,
  1 AS `estudiante_id`,
  1 AS `periodo_id`,
  1 AS `estudiante_nombre`,
  1 AS `nombre`,
  1 AS `apellido`,
  1 AS `cedula`,
  1 AS `email`,
  1 AS `carrera_nombre`,
  1 AS `periodo_nombre`,
  1 AS `fecha_creacion`,
  1 AS `fecha_envio`,
  1 AS `json_data`,
  1 AS `observaciones_admin`,
  1 AS `revisada_por_admin`,
  1 AS `fecha_revision_admin`,
  1 AS `puntaje_calculado`,
  1 AS `revisado_por`,
  1 AS `relacionada_beca` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_fichas_socioeconomicas_completa`
--

DROP TABLE IF EXISTS `v_fichas_socioeconomicas_completa`;
/*!50001 DROP VIEW IF EXISTS `v_fichas_socioeconomicas_completa`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_fichas_socioeconomicas_completa` AS SELECT
 1 AS `id`,
  1 AS `estudiante_id`,
  1 AS `periodo_id`,
  1 AS `json_data`,
  1 AS `estado`,
  1 AS `revisada_por_admin`,
  1 AS `fecha_revision_admin`,
  1 AS `observaciones_admin`,
  1 AS `fecha_creacion`,
  1 AS `fecha_envio`,
  1 AS `fecha_revision`,
  1 AS `revisado_por`,
  1 AS `puntaje_calculado`,
  1 AS `estudiante_nombre`,
  1 AS `estudiante_apellido`,
  1 AS `estudiante_cedula`,
  1 AS `estudiante_email`,
  1 AS `estudiante_telefono`,
  1 AS `carrera_nombre`,
  1 AS `periodo_nombre`,
  1 AS `periodo_inicio`,
  1 AS `periodo_fin`,
  1 AS `periodo_activo_fichas`,
  1 AS `estudiante_completo`,
  1 AS `estado_class` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_solicitudes_becas_completas`
--

DROP TABLE IF EXISTS `v_solicitudes_becas_completas`;
/*!50001 DROP VIEW IF EXISTS `v_solicitudes_becas_completas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_solicitudes_becas_completas` AS SELECT
 1 AS `id`,
  1 AS `estudiante_id`,
  1 AS `nombre_estudiante`,
  1 AS `cedula`,
  1 AS `carrera`,
  1 AS `nombre_beca`,
  1 AS `tipo_beca`,
  1 AS `monto_beca`,
  1 AS `periodo`,
  1 AS `estado`,
  1 AS `fecha_solicitud`,
  1 AS `fecha_revision`,
  1 AS `fecha_aprobacion`,
  1 AS `fecha_rechazo`,
  1 AS `porcentaje_avance`,
  1 AS `documento_actual_verificando`,
  1 AS `revisado_por`,
  1 AS `observaciones`,
  1 AS `motivo_rechazo`,
  1 AS `total_documentos`,
  1 AS `documentos_aprobados`,
  1 AS `documentos_rechazados`,
  1 AS `documentos_pendientes` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_solicitudes_becas_detallada`
--

DROP TABLE IF EXISTS `v_solicitudes_becas_detallada`;
/*!50001 DROP VIEW IF EXISTS `v_solicitudes_becas_detallada`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_solicitudes_becas_detallada` AS SELECT
 1 AS `id`,
  1 AS `estudiante_id`,
  1 AS `beca_id`,
  1 AS `periodo_id`,
  1 AS `estado`,
  1 AS `observaciones`,
  1 AS `fecha_solicitud`,
  1 AS `fecha_revision`,
  1 AS `revisado_por`,
  1 AS `motivo_rechazo`,
  1 AS `documentos_revisados`,
  1 AS `total_documentos`,
  1 AS `estudiante_nombre`,
  1 AS `estudiante_apellido`,
  1 AS `estudiante_cedula`,
  1 AS `carrera_id`,
  1 AS `carrera_nombre`,
  1 AS `beca_nombre`,
  1 AS `tipo_beca`,
  1 AS `monto_beca`,
  1 AS `periodo_nombre` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_becas_completas`
--

/*!50001 DROP VIEW IF EXISTS `v_becas_completas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_becas_completas` AS select `b`.`id` AS `id`,`b`.`nombre` AS `nombre`,`b`.`descripcion` AS `descripcion`,`b`.`tipo_beca` AS `tipo_beca`,`b`.`monto_beca` AS `monto_beca`,`b`.`cupos_disponibles` AS `cupos_disponibles`,`b`.`estado` AS `estado`,`b`.`activa` AS `activa`,`p`.`nombre` AS `periodo_vigente`,`b`.`fecha_inicio_vigencia` AS `fecha_inicio_vigencia`,`b`.`fecha_fin_vigencia` AS `fecha_fin_vigencia`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `creado_por`,`b`.`fecha_creacion` AS `fecha_creacion`,count(`sb`.`id`) AS `total_solicitudes`,count(case when `sb`.`estado` = 'Aprobada' then 1 end) AS `solicitudes_aprobadas`,count(case when `sb`.`estado` = 'Rechazada' then 1 end) AS `solicitudes_rechazadas`,count(case when `sb`.`estado` = 'En Revisi?n' then 1 end) AS `solicitudes_en_revision`,count(case when `sb`.`estado` = 'Lista de Espera' then 1 end) AS `solicitudes_lista_espera`,count(case when `sb`.`estado` = 'Postulada' then 1 end) AS `solicitudes_postuladas` from (((`becas` `b` left join `periodos_academicos` `p` on(`b`.`periodo_vigente_id` = `p`.`id`)) left join `usuarios` `u` on(`b`.`creado_por` = `u`.`id`)) left join `solicitudes_becas` `sb` on(`b`.`id` = `sb`.`beca_id`)) group by `b`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_dashboard_admin_bienestar`
--

/*!50001 DROP VIEW IF EXISTS `v_dashboard_admin_bienestar`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_dashboard_admin_bienestar` AS select `pa`.`id` AS `periodo_id`,`pa`.`nombre` AS `periodo_nombre`,`pa`.`fecha_inicio` AS `fecha_inicio`,`pa`.`fecha_fin` AS `fecha_fin`,`pa`.`limite_fichas` AS `limite_fichas`,`pa`.`limite_becas` AS `limite_becas`,`pa`.`fichas_creadas` AS `fichas_creadas`,`pa`.`becas_asignadas` AS `becas_asignadas`,`pa`.`limite_fichas` - `pa`.`fichas_creadas` AS `fichas_disponibles`,`pa`.`limite_becas` - `pa`.`becas_asignadas` AS `becas_disponibles`,case when `pa`.`fichas_creadas` >= `pa`.`limite_fichas` then 'L??mite alcanzado' when `pa`.`fichas_creadas` >= `pa`.`limite_fichas` * 0.8 then 'Casi lleno' else 'Disponible' end AS `estado_fichas`,case when `pa`.`becas_asignadas` >= `pa`.`limite_becas` then 'L??mite alcanzado' when `pa`.`becas_asignadas` >= `pa`.`limite_becas` * 0.8 then 'Casi lleno' else 'Disponible' end AS `estado_becas` from `periodos_academicos` `pa` where `pa`.`activo` = 1 order by `pa`.`fecha_inicio` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_estadisticas_sistema`
--

/*!50001 DROP VIEW IF EXISTS `v_estadisticas_sistema`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_estadisticas_sistema` AS select (select count(0) from `usuarios` where `usuarios`.`rol_id` = 3) AS `total_estudiantes`,(select count(0) from `usuarios` where `usuarios`.`rol_id` = 2) AS `total_admin_bienestar`,(select count(0) from `usuarios` where `usuarios`.`rol_id` = 1) AS `total_superadmin`,(select count(0) from `periodos_academicos` where `periodos_academicos`.`activo` = 1) AS `periodos_activos`,(select count(0) from `fichas_socioeconomicas` where `fichas_socioeconomicas`.`estado` = 'Aprobada') AS `fichas_completadas`,(select count(0) from `solicitudes_becas` where `solicitudes_becas`.`estado` in ('Postulada','En Revisi?n')) AS `becas_pendientes`,(select count(0) from `solicitudes_ayuda_mejorada` where `solicitudes_ayuda_mejorada`.`estado` in ('Abierta','En Proceso')) AS `ayudas_pendientes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_fichas_admin`
--

/*!50001 DROP VIEW IF EXISTS `v_fichas_admin`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_fichas_admin` AS select `fs`.`id` AS `id`,`fs`.`estado` AS `estado`,`fs`.`estudiante_id` AS `estudiante_id`,`fs`.`periodo_id` AS `periodo_id`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `estudiante_nombre`,`u`.`nombre` AS `nombre`,`u`.`apellido` AS `apellido`,`u`.`cedula` AS `cedula`,`u`.`email` AS `email`,coalesce(`c`.`nombre`,'Sin carrera') AS `carrera_nombre`,`p`.`nombre` AS `periodo_nombre`,`fs`.`fecha_creacion` AS `fecha_creacion`,`fs`.`fecha_envio` AS `fecha_envio`,`fs`.`json_data` AS `json_data`,`fs`.`observaciones_admin` AS `observaciones_admin`,`fs`.`revisada_por_admin` AS `revisada_por_admin`,`fs`.`fecha_revision_admin` AS `fecha_revision_admin`,`fs`.`puntaje_calculado` AS `puntaje_calculado`,`fs`.`revisado_por` AS `revisado_por`,`fs`.`relacionada_beca` AS `relacionada_beca` from (((`fichas_socioeconomicas` `fs` join `usuarios` `u` on(`u`.`id` = `fs`.`estudiante_id`)) left join `carreras` `c` on(`c`.`id` = `u`.`carrera_id`)) join `periodos_academicos` `p` on(`p`.`id` = `fs`.`periodo_id`)) where `fs`.`estado` in ('Enviada','Revisada','Aprobada','Rechazada') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_fichas_socioeconomicas_completa`
--

/*!50001 DROP VIEW IF EXISTS `v_fichas_socioeconomicas_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_fichas_socioeconomicas_completa` AS select `fs`.`id` AS `id`,`fs`.`estudiante_id` AS `estudiante_id`,`fs`.`periodo_id` AS `periodo_id`,`fs`.`json_data` AS `json_data`,`fs`.`estado` AS `estado`,`fs`.`revisada_por_admin` AS `revisada_por_admin`,`fs`.`fecha_revision_admin` AS `fecha_revision_admin`,`fs`.`observaciones_admin` AS `observaciones_admin`,`fs`.`fecha_creacion` AS `fecha_creacion`,`fs`.`fecha_envio` AS `fecha_envio`,`fs`.`fecha_revision` AS `fecha_revision`,`fs`.`revisado_por` AS `revisado_por`,`fs`.`puntaje_calculado` AS `puntaje_calculado`,`u`.`nombre` AS `estudiante_nombre`,`u`.`apellido` AS `estudiante_apellido`,`u`.`cedula` AS `estudiante_cedula`,`u`.`email` AS `estudiante_email`,`u`.`telefono` AS `estudiante_telefono`,`c`.`nombre` AS `carrera_nombre`,`p`.`nombre` AS `periodo_nombre`,`p`.`fecha_inicio` AS `periodo_inicio`,`p`.`fecha_fin` AS `periodo_fin`,`p`.`activo_fichas` AS `periodo_activo_fichas`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `estudiante_completo`,case when `fs`.`estado` = 'Borrador' then 'warning' when `fs`.`estado` = 'Enviada' then 'info' when `fs`.`estado` = 'Revisada' then 'primary' when `fs`.`estado` = 'Aprobada' then 'success' when `fs`.`estado` = 'Rechazada' then 'danger' else 'secondary' end AS `estado_class` from (((`fichas_socioeconomicas` `fs` join `usuarios` `u` on(`u`.`id` = `fs`.`estudiante_id`)) left join `carreras` `c` on(`c`.`id` = `u`.`carrera_id`)) join `periodos_academicos` `p` on(`p`.`id` = `fs`.`periodo_id`)) order by `fs`.`fecha_creacion` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_solicitudes_becas_completas`
--

/*!50001 DROP VIEW IF EXISTS `v_solicitudes_becas_completas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_solicitudes_becas_completas` AS select `sb`.`id` AS `id`,`sb`.`estudiante_id` AS `estudiante_id`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `nombre_estudiante`,`u`.`cedula` AS `cedula`,`c`.`nombre` AS `carrera`,`b`.`nombre` AS `nombre_beca`,`b`.`tipo_beca` AS `tipo_beca`,`b`.`monto_beca` AS `monto_beca`,`p`.`nombre` AS `periodo`,`sb`.`estado` AS `estado`,`sb`.`fecha_solicitud` AS `fecha_solicitud`,`sb`.`fecha_revision` AS `fecha_revision`,`sb`.`fecha_aprobacion` AS `fecha_aprobacion`,`sb`.`fecha_rechazo` AS `fecha_rechazo`,`sb`.`porcentaje_avance` AS `porcentaje_avance`,`sb`.`documento_actual_verificando` AS `documento_actual_verificando`,concat(`rev`.`nombre`,' ',`rev`.`apellido`) AS `revisado_por`,`sb`.`observaciones` AS `observaciones`,`sb`.`motivo_rechazo` AS `motivo_rechazo`,count(`sbd`.`id`) AS `total_documentos`,count(case when `sbd`.`estado` = 'Aprobado' then 1 end) AS `documentos_aprobados`,count(case when `sbd`.`estado` = 'Rechazado' then 1 end) AS `documentos_rechazados`,count(case when `sbd`.`estado` in ('Pendiente','En Revisi??n') then 1 end) AS `documentos_pendientes` from ((((((`solicitudes_becas` `sb` join `usuarios` `u` on(`sb`.`estudiante_id` = `u`.`id`)) join `carreras` `c` on(`u`.`carrera_id` = `c`.`id`)) join `becas` `b` on(`sb`.`beca_id` = `b`.`id`)) join `periodos_academicos` `p` on(`sb`.`periodo_id` = `p`.`id`)) left join `usuarios` `rev` on(`sb`.`revisado_por` = `rev`.`id`)) left join `solicitudes_becas_documentos` `sbd` on(`sb`.`id` = `sbd`.`solicitud_beca_id`)) group by `sb`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_solicitudes_becas_detallada`
--

/*!50001 DROP VIEW IF EXISTS `v_solicitudes_becas_detallada`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_solicitudes_becas_detallada` AS select `sb`.`id` AS `id`,`sb`.`estudiante_id` AS `estudiante_id`,`sb`.`beca_id` AS `beca_id`,`sb`.`periodo_id` AS `periodo_id`,`sb`.`estado` AS `estado`,`sb`.`observaciones` AS `observaciones`,`sb`.`fecha_solicitud` AS `fecha_solicitud`,`sb`.`fecha_revision` AS `fecha_revision`,`sb`.`revisado_por` AS `revisado_por`,`sb`.`motivo_rechazo` AS `motivo_rechazo`,`sb`.`documentos_revisados` AS `documentos_revisados`,`sb`.`total_documentos` AS `total_documentos`,`u`.`nombre` AS `estudiante_nombre`,`u`.`apellido` AS `estudiante_apellido`,`u`.`cedula` AS `estudiante_cedula`,`u`.`carrera_id` AS `carrera_id`,`c`.`nombre` AS `carrera_nombre`,`b`.`nombre` AS `beca_nombre`,`b`.`tipo_beca` AS `tipo_beca`,`b`.`monto_beca` AS `monto_beca`,`pa`.`nombre` AS `periodo_nombre` from ((((`solicitudes_becas` `sb` join `usuarios` `u` on(`u`.`id` = `sb`.`estudiante_id`)) left join `carreras` `c` on(`c`.`id` = `u`.`carrera_id`)) join `becas` `b` on(`b`.`id` = `sb`.`beca_id`)) join `periodos_academicos` `pa` on(`pa`.`id` = `sb`.`periodo_id`)) */;
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

-- Dump completed on 2026-07-14 18:46:48
