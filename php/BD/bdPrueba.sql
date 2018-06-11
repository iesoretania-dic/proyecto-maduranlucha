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
  `fecha_alta` datetime DEFAULT NULL,
  `fecha_baja` datetime DEFAULT NULL,
  PRIMARY KEY (`dni`),
  KEY `FK_cliente_usuario` (`id_usuario`),
  CONSTRAINT `FK_cliente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `cliente`;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` (`dni`, `id_usuario`, `nombre`, `direccion`, `telefono`, `ciudad`, `cp`, `provincia`, `eliminado`, `antenas`, `routers`, `atas`, `fecha_alta`, `fecha_baja`) VALUES
	('24070717K', '22342611C', 'MARTIN PRIETO BLANCA', 'C/ MARQUESES Nº3 5G', '654852364', 'BAÑOS', 'JAEN', '23711', 'No', 0, 0, 0, '2018-06-06 09:54:04', NULL),
	('27447172F', '25623900Z', 'RAMON SANCHEZ CARMONA', 'C/ LA PAZ Nº5', '659852471', 'BAILEN', 'JAEN', '23710', 'No', 1, 1, 0, '2018-06-06 09:46:48', NULL),
	('58532667J', '22342611C', 'CELIA CAZORLA ROMA', 'C/ SEVILLA Nº32 4F', '645987425', 'BAILEN', 'JAEN', '23710', 'No', 1, 1, 1, '2018-06-06 09:55:39', NULL),
	('66478717T', '25623900Z', 'LAURA LOPEZ BALLESTEROS', 'PSO ANDALUCIA Nº8', '687598412', 'LINARES', 'JAEN', '23700', 'No', 0, 0, 0, '2018-06-06 09:51:05', NULL),
	('85219212A', '22342611C', 'ELENA NOGUERA BERBEL', 'C/ MIGUEL HERNANDEZ', '693587412', 'LINARES', 'JAEN', '23700', 'No', 0, 0, 0, '2018-06-06 09:58:34', NULL);
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `comentarios`;
/*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
INSERT INTO `comentarios` (`id_comentario`, `id_incidencia`, `tecnico`, `texto`, `fecha`) VALUES
	(13, 112, '02598702R', 'Orientar la antena', '2018-06-06 11:15:34');
/*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;

DROP TABLE IF EXISTS `conexiones`;
CREATE TABLE IF NOT EXISTS `conexiones` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` enum('conexion','desconexion') COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_conexiones_usuario` (`usuario`),
  CONSTRAINT `FK_conexiones_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=891 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `conexiones`;
/*!40000 ALTER TABLE `conexiones` DISABLE KEYS */;
INSERT INTO `conexiones` (`id`, `usuario`, `fecha`, `tipo`) VALUES
	(883, '21418926S', '2018-06-06 11:05:47', 'conexion'),
	(884, NULL, '2018-06-06 11:07:07', 'desconexion'),
	(885, '02598702R', '2018-06-06 11:07:13', 'conexion'),
	(886, '02598702R', '2018-06-06 11:12:35', 'desconexion'),
	(887, '22342611C', '2018-06-06 11:12:38', 'conexion'),
	(888, '22342611C', '2018-06-06 11:15:07', 'desconexion'),
	(889, NULL, '2018-06-06 11:15:10', 'conexion'),
	(890, '02598702R', '2018-06-06 11:15:16', 'conexion');
/*!40000 ALTER TABLE `conexiones` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `incidencia`;
/*!40000 ALTER TABLE `incidencia` DISABLE KEYS */;
INSERT INTO `incidencia` (`id_incidencia`, `id_usuario`, `id_cliente`, `tecnico`, `fecha_creacion`, `fecha_inicio`, `fecha_resolucion`, `fecha_parcial`, `disponible`, `otros`, `tipo`, `estado`, `reincidencia`, `llamada_obligatoria`, `parcial`, `antenas`, `routers`, `atas`, `urgente`) VALUES
	(110, '02598702R', '27447172F', '21418926S', '2018-06-06 11:05:34', '2018-06-06 11:05:49', '2018-06-06 11:11:52', NULL, NULL, 'creada por un administrador', 'instalacion', '3', NULL, 'Si', 'No', 1, 1, 0, 'No'),
	(111, '22342611C', '58532667J', '21418926S', '2018-06-06 11:13:09', '2018-06-06 11:13:15', '2018-06-06 11:13:31', NULL, NULL, 'Llevar una escalera', 'instalacion', '3', NULL, 'Si', 'No', 1, 1, 1, 'No'),
	(112, '22342611C', '58532667J', '21418926S', '2018-06-06 11:14:58', '2018-06-06 11:15:39', '2018-06-06 11:16:53', NULL, NULL, 'No le llega bien la señal', 'averia', '3', NULL, 'Si', 'No', 0, 0, 0, 'No'),
	(113, '02598702R', '27447172F', NULL, '2018-06-06 11:20:35', NULL, NULL, NULL, '2018-06-06 11:20:35', 'por la noche le falla mucho el internet', 'averia', '0', NULL, 'No', 'No', NULL, NULL, NULL, 'No');
/*!40000 ALTER TABLE `incidencia` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `material`;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` (`id_material`, `id_usuario`, `fecha`, `nombre`, `contador`, `terminado`) VALUES
	(35, '21418926S', '2018-06-06 11:10:49', 'cajacable', 2, 'No'),
	(36, '21418926S', '2018-06-06 11:10:49', 'bolsaconectores', 2, 'No');
/*!40000 ALTER TABLE `material` ENABLE KEYS */;

DROP TABLE IF EXISTS `noautorizados`;
CREATE TABLE IF NOT EXISTS `noautorizados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_noautorizados_usuario` (`usuario`),
  CONSTRAINT `FK_noautorizados_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `noautorizados`;
/*!40000 ALTER TABLE `noautorizados` DISABLE KEYS */;
/*!40000 ALTER TABLE `noautorizados` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `solucion`;
/*!40000 ALTER TABLE `solucion` DISABLE KEYS */;
INSERT INTO `solucion` (`id_solucion`, `id_incidencia`, `tecnico`, `fecha`, `solucion`) VALUES
	(21, 110, '21418926S', '2018-06-06 11:11:52', '["Instalacion normal","Instalacion de antena","Instalacion de router"]'),
	(22, 111, '21418926S', '2018-06-06 11:13:31', '["Instalacion normal","Instalacion de antena","Instalacion de router","Instalacion de ata"]'),
	(23, 112, '21418926S', '2018-06-06 11:16:53', '["Orientacion de Antena"]');
/*!40000 ALTER TABLE `solucion` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DELETE FROM `stock`;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
INSERT INTO `stock` (`id_stock`, `fecha`, `antenas`, `routers`, `atas`, `ultimousuario`, `antenasM`, `routersM`, `atasM`) VALUES
	(120, '2018-06-06 11:07:25', 20, 20, 20, '02598702R', 20, 20, 20),
	(121, '2018-06-06 11:10:49', 15, 15, 15, '21418926S', -5, -5, -5);
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;

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

DELETE FROM `usuario`;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` (`dni`, `usuario`, `nombre`, `telefono`, `clave`, `rol`, `asignada`, `antenas`, `routers`, `atas`, `limite`) VALUES
	('02598702R', 'sadmin', 'JULIO ARANCE LOPEZ', '658745893', '$2y$12$NihUrF3/1LsBf84WVNRV7.xtGGRWiB.yu4wiVxvu/j1DZu8MgJ6dS', '0', NULL, NULL, NULL, NULL, NULL),
	('16937417X', 'albamaria', 'ALBA MARIA PEREZ', '632587419', '$2y$12$PcBo5wrikLqqn7k771oBz./R2I24OcCyABs6QqkRk152tJXo9dk5C', '4', NULL, NULL, NULL, NULL, NULL),
	('21418926S', 'maria', 'MARIA HERRERA GARCIA', '685297568', '$2y$12$fZfUAPmVWYF2x7P/Y5LOcexZ/sy5hrOy5783h/RjHPEsYtqeDMMmC', '2', NULL, 3, 3, 4, 5),
	('22342611C', 'angeles', 'ANGELES BARREDA SELLES', '632198745', '$2y$12$buUFlHa61srWVgss4aBup.K56SXFkPN.4z5NrOPhDaD1Px2gOlSwO', '1', NULL, NULL, NULL, NULL, NULL),
	('25623900Z', 'josemiguel', 'JOSE MIGUEL OLIVEROS CERRATO', '674185299', '$2y$12$Ek1Qsvl2PYvaFCdGIVu2cuH9BmJ/V94WCAfBLRZcMe0NM4NhTFXJu', '1', NULL, NULL, NULL, NULL, NULL),
	('38854133A', 'mario', 'MARIO NUÑEZ FERNANDEZ', '648596578', '$2y$12$ygvTTPnCryGyWQv0pKflzu8PQrOAfDfeb2FsvnPxKGuK60Vss8dda', '2', NULL, 0, 0, 0, 10),
	('78745224D', 'enrique', 'ENRIQUE MORENO RUIZ', '658741254', '$2y$12$2/lgRg5WeFObv8AJ9FvW3est.g2xsID7IO9UO1gqRbXxlUAeL.PRu', '4', NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
