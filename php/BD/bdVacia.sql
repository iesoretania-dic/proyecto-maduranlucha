/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `dni` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `id_usuario` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `ciudad` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cp` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `provincia` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `eliminado` enum('No','Si') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'No',
  `antenas` smallint(9) DEFAULT '0',
  `routers` smallint(9) DEFAULT '0',
  `atas` smallint(9) DEFAULT '0',
  `fecha_alta` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  PRIMARY KEY (`dni`),
  KEY `FK_cliente_usuario` (`id_usuario`),
  CONSTRAINT `FK_cliente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `comentarios`;
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id_comentario` smallint(10) NOT NULL AUTO_INCREMENT,
  `id_incidencia` smallint(10) NOT NULL,
  `tecnico` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `texto` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_comentario`),
  KEY `FK_comentarios_incidencia` (`id_incidencia`),
  CONSTRAINT `FK_comentarios_incidencia` FOREIGN KEY (`id_incidencia`) REFERENCES `incidencia` (`id_incidencia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `conexiones`;
CREATE TABLE IF NOT EXISTS `conexiones` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` enum('conexion','desconexion') COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_conexiones_usuario` (`usuario`),
  CONSTRAINT `FK_conexiones_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `incidencia`;
CREATE TABLE IF NOT EXISTS `incidencia` (
  `id_incidencia` smallint(10) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `id_cliente` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `tecnico` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_resolucion` datetime DEFAULT NULL,
  `fecha_parcial` datetime DEFAULT NULL,
  `disponible` datetime DEFAULT CURRENT_TIMESTAMP,
  `otros` text COLLATE utf8_spanish_ci,
  `tipo` enum('instalacion','cambiodomicilio','averia','baja','mantenimiento') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'instalacion',
  `estado` enum('0','1','2','3','4','5') COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `reincidencia` smallint(2) DEFAULT NULL,
  `llamada_obligatoria` enum('No','Si') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'No',
  `parcial` enum('No','Si') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'No',
  `antenas` smallint(10) DEFAULT NULL,
  `routers` smallint(10) DEFAULT NULL,
  `atas` smallint(10) DEFAULT NULL,
  `urgente` enum('No','Si') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id_incidencia`),
  KEY `FK_incidencia_usuario` (`id_usuario`),
  KEY `FK_incidencia_cliente` (`id_cliente`),
  KEY `FK_incidencia_usuario_2` (`tecnico`),
  CONSTRAINT `FK_incidencia_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_incidencia_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_incidencia_usuario_2` FOREIGN KEY (`tecnico`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `llamadas`;
CREATE TABLE IF NOT EXISTS `llamadas` (
  `id_llamada` smallint(10) NOT NULL AUTO_INCREMENT,
  `id_incidencia` smallint(10) NOT NULL,
  `id_usuario` varchar(9) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_llamada`),
  KEY `FK_llamadas_incidencia` (`id_incidencia`),
  KEY `FK_llamadas_usuario` (`id_usuario`),
  CONSTRAINT `FK_llamadas_incidencia` FOREIGN KEY (`id_incidencia`) REFERENCES `incidencia` (`id_incidencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_llamadas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `material`;
CREATE TABLE IF NOT EXISTS `material` (
  `id_material` smallint(10) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nombre` enum('router','antena','cajacable','bolsaconectores') COLLATE utf8_spanish_ci NOT NULL,
  `contador` smallint(6) NOT NULL DEFAULT '0',
  `terminado` enum('No','Si') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id_material`),
  KEY `FK_material_usuario` (`id_usuario`),
  CONSTRAINT `FK_material_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `noautorizados`;
CREATE TABLE IF NOT EXISTS `noautorizados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_noautorizados_usuario` (`usuario`),
  CONSTRAINT `FK_noautorizados_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `solucion`;
CREATE TABLE IF NOT EXISTS `solucion` (
  `id_solucion` smallint(9) NOT NULL AUTO_INCREMENT,
  `id_incidencia` smallint(9) NOT NULL,
  `tecnico` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solucion` text COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id_solucion`),
  UNIQUE KEY `id_incidencia` (`id_incidencia`),
  KEY `FK_solucion_incidencia` (`id_incidencia`),
  KEY `FK_solucion_usuario` (`tecnico`),
  CONSTRAINT `FK_solucion_incidencia` FOREIGN KEY (`id_incidencia`) REFERENCES `incidencia` (`id_incidencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_solucion_usuario` FOREIGN KEY (`tecnico`) REFERENCES `usuario` (`dni`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id_stock` smallint(10) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `antenas` smallint(6) NOT NULL DEFAULT '0',
  `routers` smallint(6) NOT NULL DEFAULT '0',
  `atas` smallint(6) NOT NULL DEFAULT '0',
  `ultimousuario` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `antenasM` smallint(6) NOT NULL,
  `routersM` smallint(6) NOT NULL,
  `atasM` smallint(6) NOT NULL,
  PRIMARY KEY (`id_stock`),
  KEY `FK_stock_usuario` (`ultimousuario`),
  CONSTRAINT `FK_stock_usuario` FOREIGN KEY (`ultimousuario`) REFERENCES `usuario` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `dni` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `clave` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `rol` enum('0','1','2','3','4') COLLATE utf8_spanish_ci NOT NULL,
  `asignada` smallint(10) DEFAULT NULL,
  `antenas` smallint(10) DEFAULT NULL,
  `routers` smallint(10) DEFAULT NULL,
  `atas` smallint(10) DEFAULT NULL,
  `limite` smallint(10) DEFAULT NULL,
  PRIMARY KEY (`dni`),
  UNIQUE KEY `nombre` (`usuario`),
  KEY `FK_usuario_incidencia` (`asignada`),
  CONSTRAINT `FK_usuario_incidencia` FOREIGN KEY (`asignada`) REFERENCES `incidencia` (`id_incidencia`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
