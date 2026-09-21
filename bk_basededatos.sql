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


-- Volcando estructura de base de datos para sistema_moda
CREATE DATABASE IF NOT EXISTS `sistema_moda` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `sistema_moda`;

-- Volcando estructura para tabla sistema_moda.caja_sesiones
CREATE TABLE IF NOT EXISTS `caja_sesiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `sucursal_id` int NOT NULL DEFAULT '1',
  `monto_apertura` decimal(10,2) NOT NULL,
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `fecha_apertura` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` datetime DEFAULT NULL,
  `total_ventas_sistema` decimal(10,2) DEFAULT '0.00',
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('abierta','cerrada') DEFAULT 'abierta',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `fk_caja_sucursales` (`sucursal_id`),
  CONSTRAINT `caja_sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_caja_sucursales` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.caja_sesiones: ~0 rows (aproximadamente)
DELETE FROM `caja_sesiones`;
INSERT INTO `caja_sesiones` (`id`, `usuario_id`, `sucursal_id`, `monto_apertura`, `monto_cierre`, `fecha_apertura`, `fecha_cierre`, `total_ventas_sistema`, `diferencia`, `estado`) VALUES
	(1, 3, 1, 0.00, NULL, '2026-03-13 20:56:46', NULL, 0.00, NULL, 'abierta');

-- Volcando estructura para tabla sistema_moda.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.categorias: ~2 rows (aproximadamente)
DELETE FROM `categorias`;
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activo`) VALUES
	(1, 'Camisas', 'Camisas de vestir y casuales para hombre y mujer.', 1),
	(2, 'Pantalones', 'Pantalones de vestir, jeans y deportivos.', 1);

-- Volcando estructura para tabla sistema_moda.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` text,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.clientes: ~3 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `nombre`, `documento`, `telefono`, `correo`, `direccion`, `activo`, `fecha_registro`) VALUES
	(1, 'Público General', '00000000', NULL, NULL, NULL, 1, '2025-11-23 11:24:25'),
	(2, 'María López', '71234567', '955555555', 'maria.lopez@email.com', 'Av. Las Flores 789.', 1, '2026-03-13 20:47:14'),
	(3, 'Carlos Mendoza', '45678912', '944444444', 'carlos.m@email.com', 'Urb. Los Cedros Mz A Lt 5.', 1, '2026-03-13 20:47:39');

-- Volcando estructura para tabla sistema_moda.compras
CREATE TABLE IF NOT EXISTS `compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proveedor_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `sucursal_id` int NOT NULL DEFAULT '1',
  `numero_comprobante` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `fk_compras_sucursales` (`sucursal_id`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_compras_sucursales` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.compras: ~0 rows (aproximadamente)
DELETE FROM `compras`;
INSERT INTO `compras` (`id`, `proveedor_id`, `usuario_id`, `sucursal_id`, `numero_comprobante`, `total`, `fecha`) VALUES
	(1, 3, 3, 1, 'B002-0555', 600.00, '2026-03-13 20:53:54'),
	(2, 2, 3, 1, 'F001-0001', 900.00, '2026-03-13 20:54:41');

-- Volcando estructura para tabla sistema_moda.compra_detalles
CREATE TABLE IF NOT EXISTS `compra_detalles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compra_id` int NOT NULL,
  `variante_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_id` (`compra_id`),
  KEY `variante_id` (`variante_id`),
  CONSTRAINT `compra_detalles_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`),
  CONSTRAINT `compra_detalles_ibfk_2` FOREIGN KEY (`variante_id`) REFERENCES `producto_variantes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.compra_detalles: ~4 rows (aproximadamente)
DELETE FROM `compra_detalles`;
INSERT INTO `compra_detalles` (`id`, `compra_id`, `variante_id`, `cantidad`, `precio_compra`, `subtotal`) VALUES
	(1, 1, 3, 5, 60.00, 300.00),
	(2, 1, 4, 5, 60.00, 300.00),
	(3, 2, 1, 10, 45.00, 450.00),
	(4, 2, 2, 10, 45.00, 450.00);

-- Volcando estructura para tabla sistema_moda.empresa
CREATE TABLE IF NOT EXISTS `empresa` (
  `id` int NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `direccion` text,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mensaje_ticket` text,
  `logo` varchar(255) DEFAULT NULL,
  `moneda` varchar(5) DEFAULT 'S/',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.empresa: ~1 rows (aproximadamente)
DELETE FROM `empresa`;
INSERT INTO `empresa` (`id`, `nombre`, `ruc`, `direccion`, `telefono`, `email`, `mensaje_ticket`, `logo`, `moneda`) VALUES
	(1, 'MI TIENDA DE MODA', '20000000001', 'Av. Principal 123', '999-000-000', 'contacto@tienda.com', '¡Vuelva pronto!\r\n!Feliz Navidad¡', 'img/logo_1772126695.png', 'S/');

-- Volcando estructura para tabla sistema_moda.gastos
CREATE TABLE IF NOT EXISTS `gastos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caja_sesion_id` int NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int NOT NULL,
  `sucursal_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `caja_sesion_id` (`caja_sesion_id`),
  KEY `fk_gastos_sucursales` (`sucursal_id`),
  CONSTRAINT `fk_gastos_sucursales` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`caja_sesion_id`) REFERENCES `caja_sesiones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.gastos: ~0 rows (aproximadamente)
DELETE FROM `gastos`;
INSERT INTO `gastos` (`id`, `caja_sesion_id`, `descripcion`, `monto`, `fecha`, `usuario_id`, `sucursal_id`) VALUES
	(1, 1, 'PASAJE DELIVERY', 5.00, '2026-03-13 21:11:13', 3, 1);

-- Volcando estructura para tabla sistema_moda.inventario_sucursales
CREATE TABLE IF NOT EXISTS `inventario_sucursales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sucursal_id` int NOT NULL,
  `variante_id` int NOT NULL,
  `stock_actual` int DEFAULT '0',
  `stock_minimo` int DEFAULT '5',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sucursal_variante` (`sucursal_id`,`variante_id`),
  KEY `fk_inv_variantes` (`variante_id`),
  CONSTRAINT `fk_inv_sucursales` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `fk_inv_variantes` FOREIGN KEY (`variante_id`) REFERENCES `producto_variantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.inventario_sucursales: ~4 rows (aproximadamente)
DELETE FROM `inventario_sucursales`;
INSERT INTO `inventario_sucursales` (`id`, `sucursal_id`, `variante_id`, `stock_actual`, `stock_minimo`) VALUES
	(1, 1, 1, 10, 5),
	(2, 1, 2, 9, 5),
	(3, 1, 3, 3, 5),
	(4, 1, 4, 4, 5);

-- Volcando estructura para tabla sistema_moda.kardex
CREATE TABLE IF NOT EXISTS `kardex` (
  `id` int NOT NULL AUTO_INCREMENT,
  `variante_id` int NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `cantidad` int NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int DEFAULT NULL,
  `sucursal_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `variante_id` (`variante_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `fk_kardex_sucursales` (`sucursal_id`),
  CONSTRAINT `fk_kardex_sucursales` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `kardex_ibfk_1` FOREIGN KEY (`variante_id`) REFERENCES `producto_variantes` (`id`),
  CONSTRAINT `kardex_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.kardex: ~6 rows (aproximadamente)
DELETE FROM `kardex`;
INSERT INTO `kardex` (`id`, `variante_id`, `tipo`, `cantidad`, `descripcion`, `fecha`, `usuario_id`, `sucursal_id`) VALUES
	(1, 3, 'entrada', 5, 'Ingreso por Compra #000001 / Factura: B002-0555', '2026-03-13 20:53:54', 3, 1),
	(2, 4, 'entrada', 5, 'Ingreso por Compra #000001 / Factura: B002-0555', '2026-03-13 20:53:54', 3, 1),
	(3, 1, 'entrada', 10, 'Ingreso por Compra #000002 / Factura: F001-0001', '2026-03-13 20:54:41', 3, 1),
	(4, 2, 'entrada', 10, 'Ingreso por Compra #000002 / Factura: F001-0001', '2026-03-13 20:54:41', 3, 1),
	(5, 2, 'salida', 1, 'Venta Ticket #000001', '2026-03-13 20:58:01', 3, 1),
	(6, 4, 'salida', 1, 'Venta Ticket #000001', '2026-03-13 20:58:01', 3, 1),
	(7, 3, 'salida', 2, 'Venta Ticket #000002', '2026-03-13 21:10:30', 3, 1);

-- Volcando estructura para tabla sistema_moda.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo_barras_base` varchar(50) DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text,
  `activo` tinyint(1) DEFAULT '1',
  `categoria_id` int DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.productos: ~2 rows (aproximadamente)
DELETE FROM `productos`;
INSERT INTO `productos` (`id`, `codigo_barras_base`, `nombre`, `descripcion`, `activo`, `categoria_id`, `precio_compra`, `precio_venta`, `imagen`, `fecha_creacion`) VALUES
	(1, 'CAM001', 'Camisa Oxford Manga Larga', 'Camisa Oxford', 1, 1, 45.00, 80.00, NULL, '2026-03-13 20:50:14'),
	(2, 'PAN001', 'Pantalón Jean Slim Fit', 'Pantalón Jean', 1, 2, 60.00, 110.00, NULL, '2026-03-13 20:52:00');

-- Volcando estructura para tabla sistema_moda.producto_imagenes
CREATE TABLE IF NOT EXISTS `producto_imagenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `es_principal` tinyint(1) DEFAULT '0',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `producto_imagenes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando estructura para tabla sistema_moda.producto_variantes
CREATE TABLE IF NOT EXISTS `producto_variantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL,
  `talla` varchar(10) NOT NULL,
  `color` varchar(50) NOT NULL,
  `stock_actual` int DEFAULT '0',
  `stock_minimo` int DEFAULT '5',
  `codigo_barras_variante` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `producto_variantes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.producto_variantes: ~4 rows (aproximadamente)
DELETE FROM `producto_variantes`;
INSERT INTO `producto_variantes` (`id`, `producto_id`, `talla`, `color`, `stock_actual`, `stock_minimo`, `codigo_barras_variante`) VALUES
	(1, 1, 'M', 'BLANCO', 0, 5, '1-M-BLA'),
	(2, 1, 'L', 'CELESTE', 0, 5, '1-L-CEL'),
	(3, 2, '32', 'AZUL NOCHE', 0, 5, '2-32-AZU'),
	(4, 2, '34', 'NEGRO', 0, 5, '2-34-NEG');

-- Volcando estructura para tabla sistema_moda.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(20) DEFAULT NULL,
  `razon_social` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` text,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ruc` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.proveedores: ~3 rows (aproximadamente)
DELETE FROM `proveedores`;
INSERT INTO `proveedores` (`id`, `ruc`, `razon_social`, `telefono`, `correo`, `direccion`, `activo`, `fecha_registro`) VALUES
	(1, '00000000000', 'Proveedor General', NULL, NULL, 'Local', 1, '2025-11-23 13:01:24'),
	(2, '20123456781', 'Textil Distribuciones S.A.C.', '987654321', 'ventas@textildistribuciones.com', 'Av. Industrial 456, Lima.', 1, '2026-03-13 20:45:48'),
	(3, '10987654321', 'Importaciones Moda Global', '912345678', 'contacto@modaglobal.com', 'Calle Comercio 123, Arequipa.', 1, '2026-03-13 20:46:21');

-- Volcando estructura para tabla sistema_moda.sucursales
CREATE TABLE IF NOT EXISTS `sucursales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `direccion` text,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.sucursales: ~2 rows (aproximadamente)
DELETE FROM `sucursales`;
INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `activo`) VALUES
	(1, 'Sucursal Principal', 'Dirección Principal', '00000000', 1),
	(6, 'Sucursal Centro', 'LIMA - PERÚ', '90909090', 1);

-- Volcando estructura para tabla sistema_moda.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','vendedor') DEFAULT 'vendedor',
  `sucursal_id` int DEFAULT '1',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`email`),
  KEY `fk_usuarios_sucursales` (`sucursal_id`),
  CONSTRAINT `fk_usuarios_sucursales` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.usuarios: ~2 rows (aproximadamente)
DELETE FROM `usuarios`;
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `sucursal_id`, `activo`) VALUES
	(3, 'Admin Principal', 'admin@tienda.com', '$2y$10$uw8PETVysGcwnKzoEH3HxualJv46S8/.evhQkepuHOdfhmIntrShe', 'admin', 1, 1);

-- Volcando estructura para tabla sistema_moda.ventas
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `cliente_id` int DEFAULT '1',
  `sucursal_id` int NOT NULL DEFAULT '1',
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) DEFAULT NULL,
  `forma_pago` varchar(50) DEFAULT 'efectivo',
  `estado` enum('completada','anulada') DEFAULT 'completada',
  `motivo_anulacion` text,
  `fecha_anulacion` datetime DEFAULT NULL,
  `usuario_anulacion_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `fk_ventas_sucursales` (`sucursal_id`),
  CONSTRAINT `fk_ventas_sucursales` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.ventas: ~1 rows (aproximadamente)
DELETE FROM `ventas`;
INSERT INTO `ventas` (`id`, `usuario_id`, `cliente_id`, `sucursal_id`, `fecha`, `total`, `forma_pago`, `estado`, `motivo_anulacion`, `fecha_anulacion`, `usuario_anulacion_id`) VALUES
	(1, 3, 2, 1, '2026-03-13 20:58:01', 190.00, 'efectivo', 'completada', NULL, NULL, NULL),
	(2, 3, 2, 1, '2026-03-13 21:10:30', 220.00, 'efectivo', 'completada', NULL, NULL, NULL);

-- Volcando estructura para tabla sistema_moda.venta_detalles
CREATE TABLE IF NOT EXISTS `venta_detalles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venta_id` int NOT NULL,
  `variante_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `variante_id` (`variante_id`),
  CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  CONSTRAINT `venta_detalles_ibfk_2` FOREIGN KEY (`variante_id`) REFERENCES `producto_variantes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla sistema_moda.venta_detalles: ~2 rows (aproximadamente)
DELETE FROM `venta_detalles`;
INSERT INTO `venta_detalles` (`id`, `venta_id`, `variante_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
	(1, 1, 2, 1, 80.00, 80.00),
	(2, 1, 4, 1, 110.00, 110.00),
	(3, 2, 3, 2, 110.00, 220.00);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
