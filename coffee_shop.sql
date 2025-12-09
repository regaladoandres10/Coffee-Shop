-- -----------------------------------------------------
-- Base de datos corregida
-- Compatible con MySQL 5.7, MySQL 8 y MariaDB
-- -----------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. ROLES
-- =====================================================
CREATE TABLE `roles` (
  `idRol` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255),
  `isActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`idRol`, `name`, `description`, `isActive`) VALUES
(1, 'Administrador', 'Acceso completo', 1),
(2, 'Mesero', 'Toma órdenes y atiende mesas', 1),
(3, 'Cajero', 'Opera caja y pagos', 1);

-- =====================================================
-- 2. PERMISSIONS
-- =====================================================
CREATE TABLE `permissions` (
  `idPermission` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255),
  PRIMARY KEY (`idPermission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`idPermission`, `name`, `description`) VALUES
(1, 'create_users', 'Crear usuarios'),
(2, 'edit_users', 'Editar usuarios'),
(3, 'delete_users', 'Eliminar usuarios'),
(4, 'view_reports', 'Ver reportes');

-- =====================================================
-- 3. ROLE_PERMISSIONS
-- =====================================================
CREATE TABLE `role_permissions` (
  `idRol` int NOT NULL,
  `idPermission` int NOT NULL,
  PRIMARY KEY (`idRol`,`idPermission`),
  FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`) ON DELETE CASCADE,
  FOREIGN KEY (`idPermission`) REFERENCES `permissions` (`idPermission`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_permissions` (`idRol`, `idPermission`) VALUES
(1,1),(1,2),(1,3),(1,4),  -- Administrador
(2,4),                    -- Mesero
(3,4);                    -- Cajero

-- =====================================================
-- 4. USERS
-- =====================================================
CREATE TABLE `users` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `rolId` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255),
  `password` varchar(255),
  `phone` char(10),
  `address` varchar(255),
  `isActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idUser`),
  FOREIGN KEY (`rolId`) REFERENCES `roles` (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`idUser`, `rolId`, `name`, `email`, `password`, `phone`, `address`, `isActive`) VALUES
(1, 1, 'Admin', 'admin@example.com', '123456', '1234567890', 'Dirección admin', 1),
(2, 2, 'Luis', 'luis@example.com', '123456', '7295403230', 'Calle A', 1);

-- =====================================================
-- 5. TABLES (MESAS)
-- =====================================================
CREATE TABLE `tables` (
  `idTable` int NOT NULL AUTO_INCREMENT,
  `number` int NOT NULL,
  `capacity` int DEFAULT 4,
  `status` enum('disponible','ocupada','reservada') DEFAULT 'disponible',
  PRIMARY KEY (`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tables` (`idTable`, `number`, `capacity`, `status`) VALUES
(1, 1, 4, 'disponible'),
(2, 2, 6, 'ocupada'),
(3, 3, 2, 'reservada');

-- =====================================================
-- 6. CATEGORIES
-- =====================================================
CREATE TABLE `categories` (
  `idCategorie` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(200),
  `image` varchar(255),
  `isActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idCategorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`idCategorie`, `name`, `description`, `image`, `isActive`) VALUES
(1, 'Bebidas', 'Bebidas frías y calientes', NULL, 1),
(2, 'Comidas', 'Platos principales', NULL, 1),
(3, 'Postres', 'Postres dulces', NULL, 1);

-- =====================================================
-- 7. PRODUCTS
-- =====================================================
CREATE TABLE `products` (
  `idProduct` int NOT NULL AUTO_INCREMENT,
  `categorieId` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255),
  `price` decimal(10,2),
  `image` varchar(255),
  `isActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idProduct`),
  FOREIGN KEY (`categorieId`) REFERENCES `categories` (`idCategorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`idProduct`, `categorieId`, `name`, `description`, `price`, `image`, `isActive`) VALUES
(1, 1, 'Café americano', 'Café negro', 25.00, NULL, 1),
(2, 2, 'Hamburguesa', 'Carne de res', 80.00, NULL, 1),
(3, 3, 'Pastel de chocolate', 'Rebanada', 50.00, NULL, 1);

-- =====================================================
-- 8. ORDERS
-- =====================================================
CREATE TABLE `orders` (
  `idOrder` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `tableId` int NOT NULL,
  `staffId` int,
  `total` decimal(10,2),
  `status` enum('pendiente','pagada','cancelada') DEFAULT 'pendiente',
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idOrder`),
  FOREIGN KEY (`userId`) REFERENCES `users` (`idUser`),
  FOREIGN KEY (`tableId`) REFERENCES `tables` (`idTable`),
  FOREIGN KEY (`staffId`) REFERENCES `users` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`idOrder`, `userId`, `tableId`, `staffId`, `total`, `status`, `createdAt`) VALUES
(1, 2, 2, 1, 105.00, 'pendiente', NOW());

-- =====================================================
-- 9. ORDER_DETAILS
-- =====================================================
CREATE TABLE `order_details` (
  `idDetail` int NOT NULL AUTO_INCREMENT,
  `orderId` int NOT NULL,
  `productId` int NOT NULL,
  `quantity` int DEFAULT 1,
  `subtotal` decimal(10,2),
  PRIMARY KEY (`idDetail`),
  FOREIGN KEY (`orderId`) REFERENCES `orders` (`idOrder`) ON DELETE CASCADE,
  FOREIGN KEY (`productId`) REFERENCES `products` (`idProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_details` (`idDetail`, `orderId`, `productId`, `quantity`, `subtotal`) VALUES
(1, 1, 1, 1, 25.00),
(2, 1, 2, 1, 80.00);

-- =====================================================
-- 10. RESERVATIONS
-- =====================================================
CREATE TABLE `reservations` (
  `idReservation` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `tableId` int NOT NULL,
  `dateTime` datetime NOT NULL,
  `status` enum('pendiente','confirmada','cancelada','completada') DEFAULT 'pendiente',
  PRIMARY KEY (`idReservation`),
  FOREIGN KEY (`userId`) REFERENCES `users` (`idUser`),
  FOREIGN KEY (`tableId`) REFERENCES `tables` (`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reservations` (`idReservation`, `userId`, `tableId`, `dateTime`, `status`) VALUES
(1, 2, 3, '2025-12-08 15:00:00', 'confirmada');

SET FOREIGN_KEY_CHECKS = 1;
