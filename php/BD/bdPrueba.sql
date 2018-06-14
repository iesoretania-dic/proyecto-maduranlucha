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

DELETE FROM `cliente`;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` (`dni`, `id_usuario`, `nombre`, `direccion`, `telefono`, `ciudad`, `cp`, `provincia`, `eliminado`, `antenas`, `routers`, `atas`, `fecha_alta`, `fecha_baja`) VALUES
  ('00061715Y', '22623964Z', 'ALVARO VALLS CHAMORRO', 'VíA DE ESPAñA, 99', '690756237', 'BAILEN', 'JAEN', '23710', 'No', 0, 0, 0, '2018-06-14', NULL),
  ('02540484L', '34114901J', 'VICTOR CATALAN RUBIO', 'PSO AVENIDA MAYOR, 97', '605195835', 'LINARES', 'JAEN', '23700', 'No', 0, 0, 0, '2018-06-14', NULL),
  ('03288763Q', '34114901J', 'BENITO VILLAR PELAEZ', 'C/ RONDA IGLESIA, 29', '666279246', 'BAILEN', 'JAEN', '23710', 'No', 0, 0, 0, '2018-06-14', NULL),
  ('04027257A', '22623964Z', 'ANDREU BAUTISTA VICENTE', 'CAMINO IGLESIA, 31', '697651310', 'LINARES', 'JAEN', '23700', 'No', 0, 0, 0, '2018-06-14', NULL),
  ('29126849V', '22623964Z', 'LETICIA SALGUERO CEREZO', 'PASAJE HORNO, 3', '682455185', 'LINARES', 'JAEN', '23700', 'No', 0, 0, 0, '2018-06-14', NULL),
  ('39470299E', '34114901J', 'JUDITH CANOVAS OTERO', 'PLZA RAMBLA HORNO, 19', '739563111', 'BAILEN', 'JAEN', '23710', 'No', 0, 1, 0, '2018-06-14', NULL),
  ('60954124F', '22623964Z', 'LEIRE SILVA VALENZUELA', 'VEREDA REAL, 39', '647907780', 'BAILEN', 'JAEN', '23710', 'No', 1, 1, 0, '2018-06-14', NULL),
  ('62080672S', '22623964Z', 'MARIA JESUS CUEVAS GUERRERO', 'VEREDA IGLESIA, 61', '667964923', 'BAÑOS', 'JAEN', '26320', 'No', 0, 0, 0, '2018-06-14', NULL),
  ('68294467S', '34114901J', 'OLIVIA VAZQUEZ BELLO', 'PLZA MAYOR, 28', '687706071', 'BAÑOS', 'JAEN', '26320', 'No', 0, 0, 0, '2018-06-14', NULL),
  ('93289168H', '34114901J', 'MARIA VAZQUEZ NAVARRETE', 'PLZA MADRID, 36', '757828245', 'LINARES', 'JAEN', '23700', 'No', 1, 1, 1, '2018-06-14', NULL);
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
  (1, 1, '64458426Y', 'ir la semana que viene', '2018-06-14 17:46:04'),
  (2, 11, '26242341P', 'orientar la antena', '2018-06-14 17:50:32');
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
  (1, '26242341P', '2018-06-14 15:42:10', 'conexion'),
  (2, '26242341P', '2018-06-14 15:49:17', 'desconexion'),
  (3, '34114901J', '2018-06-14 15:49:23', 'conexion'),
  (4, '34114901J', '2018-06-14 16:14:56', 'desconexion'),
  (5, '22623964Z', '2018-06-14 16:15:11', 'conexion'),
  (6, '26242341P', '2018-06-14 17:41:56', 'conexion'),
  (7, '26242341P', '2018-06-14 17:44:02', 'desconexion'),
  (8, '64458426Y', '2018-06-14 17:44:11', 'conexion'),
  (9, '64458426Y', '2018-06-14 17:47:31', 'desconexion'),
  (10, '54025883X', '2018-06-14 17:47:45', 'conexion'),
  (11, '54025883X', '2018-06-14 17:48:27', 'desconexion'),
  (12, '26242341P', '2018-06-14 17:48:36', 'conexion'),
  (13, '26242341P', '2018-06-14 17:51:37', 'desconexion'),
  (14, '64458426Y', '2018-06-14 17:51:56', 'conexion'),
  (15, '64458426Y', '2018-06-14 17:53:00', 'desconexion'),
  (16, '26242341P', '2018-06-14 17:53:06', 'conexion'),
  (17, '26242341P', '2018-06-14 18:39:06', 'conexion'),
  (18, '26242341P', '2018-06-14 18:39:10', 'desconexion'),
  (19, '26242341P', '2018-06-14 18:41:35', 'conexion');
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
  (1, '34114901J', '03288763Q', '64458426Y', '2018-06-14 15:50:39', '2018-06-14 17:44:12', NULL, NULL, '2018-06-18 09:00:39', 'Ir por la tarde', 'instalacion', '2', NULL, 'Si', 'No', NULL, NULL, NULL, 'No'),
  (2, '34114901J', '39470299E', '64458426Y', '2018-06-14 16:10:20', '2018-06-14 17:46:06', '2018-06-14 17:46:56', NULL, NULL, 'ejemplo', 'instalacion', '3', NULL, 'Si', 'No', 0, 1, 0, 'No'),
  (3, '34114901J', '93289168H', '54025883X', '2018-06-14 16:11:16', '2018-06-14 17:47:46', '2018-06-14 17:48:12', NULL, NULL, 'llevar una escalera alta', 'instalacion', '3', NULL, 'Si', 'No', 1, 1, 1, 'No'),
  (4, '34114901J', '02540484L', NULL, '2018-06-14 16:12:30', '2018-06-14 17:48:22', NULL, NULL, '2018-06-14 16:12:30', 'ejemplo', 'instalacion', '1', NULL, 'No', 'No', NULL, NULL, NULL, 'No'),
  (5, '34114901J', '68294467S', NULL, '2018-06-14 16:13:54', NULL, NULL, NULL, '2018-06-14 16:13:54', 'ejemplo', 'instalacion', '1', NULL, 'No', 'No', NULL, NULL, NULL, 'No'),
  (6, '22623964Z', '29126849V', NULL, '2018-06-14 16:16:14', NULL, NULL, NULL, '2018-06-14 16:16:14', 'ejemplo', 'instalacion', '1', NULL, 'No', 'No', NULL, NULL, NULL, 'No'),
  (7, '22623964Z', '60954124F', '64458426Y', '2018-06-14 16:17:16', '2018-06-14 17:52:03', '2018-06-14 17:52:12', NULL, NULL, 'a partir de las 11:00AM', 'instalacion', '3', NULL, 'Si', 'No', 1, 1, 0, 'No'),
  (8, '22623964Z', '00061715Y', NULL, '2018-06-14 16:17:59', NULL, NULL, NULL, '2018-06-14 16:17:59', 'ejemplo', 'instalacion', '1', NULL, 'No', 'No', NULL, NULL, NULL, 'No'),
  (9, '22623964Z', '62080672S', NULL, '2018-06-14 16:18:51', NULL, NULL, NULL, '2018-06-14 16:18:51', 'ejemplo', 'instalacion', '1', NULL, 'No', 'No', NULL, NULL, NULL, 'No'),
  (10, '22623964Z', '04027257A', NULL, '2018-06-14 16:19:47', NULL, NULL, NULL, '2018-06-14 16:19:47', 'ejemplo', 'instalacion', '1', NULL, 'No', 'No', NULL, NULL, NULL, 'No'),
  (11, '26242341P', '39470299E', '64458426Y', '2018-06-14 17:49:05', '2018-06-14 17:52:33', '2018-06-14 17:52:50', NULL, NULL, 'le falla el internet por la tarde', 'averia', '3', NULL, 'Si', 'No', 0, 0, 0, 'No'),
  (12, '26242341P', '93289168H', NULL, '2018-06-14 17:49:45', '2018-06-14 17:52:52', NULL, NULL, '2018-06-14 17:49:45', 'se muda', 'cambiodomicilio', '1', NULL, 'No', 'No', NULL, NULL, NULL, 'No');
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

DELETE FROM `llamadas`;
/*!40000 ALTER TABLE `llamadas` DISABLE KEYS */;
INSERT INTO `llamadas` (`id_llamada`, `id_incidencia`, `id_usuario`, `fecha`) VALUES
  (4, 1, '64458426Y', '2018-06-14 17:44:15'),
  (5, 2, '64458426Y', '2018-06-14 17:46:08'),
  (6, 3, '54025883X', '2018-06-14 17:47:47'),
  (7, 7, '64458426Y', '2018-06-14 17:52:05'),
  (8, 11, '64458426Y', '2018-06-14 17:52:34');
/*!40000 ALTER TABLE `llamadas` ENABLE KEYS */;

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
  (1, '64458426Y', '2018-06-14 17:46:34', 'cajacable', 2, 'No'),
  (2, '64458426Y', '2018-06-14 17:46:34', 'bolsaconectores', 2, 'No'),
  (3, '54025883X', '2018-06-14 17:48:00', 'cajacable', 1, 'No'),
  (4, '54025883X', '2018-06-14 17:48:00', 'bolsaconectores', 1, 'No');
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
  (1, 2, '64458426Y', '2018-06-14 17:46:56', '["Instalacion normal","Instalacion de router"]'),
  (2, 3, '54025883X', '2018-06-14 17:48:12', '["Instalacion normal","Instalacion de antena","Instalacion de router","Instalacion de ata"]'),
  (3, 7, '64458426Y', '2018-06-14 17:52:12', '["Instalacion normal","Instalacion de antena","Instalacion de router"]'),
  (4, 11, '64458426Y', '2018-06-14 17:52:50', '["Orientacion de Antena"]');
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
  (1, '2018-06-14 17:42:09', 100, 100, 100, '26242341P', 100, 100, 100),
  (2, '2018-06-14 17:46:34', 90, 90, 91, '64458426Y', -10, -10, -9),
  (3, '2018-06-14 17:48:00', 75, 75, 76, '54025883X', -15, -15, -15);
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
  ('22037332C', 'oscar', 'OSCAR ROCA MORAL', '674715696', '$2y$12$QQB1CO8xtvRDJFQLsPFu4OIMhkfBwo13vBRblfGnaQ1YJe57iT/BG', '4', NULL, NULL, NULL, NULL, NULL),
  ('22623964Z', 'jose', 'JOSE VELAZQUEZ MENDEZ', '651900279', '$2y$12$l/MTidhpdR2bH8dtHjSUXOTBuBuXjp.5YOHfbeXodSu.evmNrFUm6', '1', NULL, NULL, NULL, NULL, NULL),
  ('26242341P', 'miguel', 'Miguel Ángel Durán Lucha', '666555444', '$2y$12$FovSsWKkhcj4g/Szy60cP.wD1ead6jqa.985Wh2NDLRv5QYMn0.n2', '0', NULL, NULL, NULL, NULL, NULL),
  ('34114901J', 'judit', 'JUDIT PEINADO FALCON', '677173351', '$2y$12$PWMDn.GFzH7pzdLgTxCyZOW8iNR/rCb/1MbHFx34RZzwqFdCi0N4u', '1', NULL, NULL, NULL, NULL, NULL),
  ('54025883X', 'felix', 'FELIX ALCAIDE REBOLLO', '667828717', '$2y$12$5bdW6dVLOWRZTajDulTeh.x35uB.iCTKqnuc8siiAeNwn8mL/wFiO', '2', NULL, 14, 14, 14, 15),
  ('64458426Y', 'maria', 'MARIA MARIN ANGULO', '722593214', '$2y$12$stSTTn7AJzHhtQb3kHD2KOnYnse7d1yX2kC6eEyc4Gq58X5awzR2m', '2', NULL, 9, 8, 9, 10),
  ('82165119B', 'aurelia', 'AURELIA DE LA FUENTE DEL RIO', '739237164', '$2y$12$L66TMQUjJw6SPAYWTAcVLOXWiIbxw1SD/ZqeZnjZgimxtFjObRz56', '4', NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
