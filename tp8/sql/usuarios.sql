-- ============================================================
-- TP8 - Tabla de usuarios para autenticación
-- Base separada para no pisar la tabla "usuarios" del TP6/TP7
-- ============================================================
CREATE DATABASE IF NOT EXISTS tanianailsart_auth
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tanianailsart_auth;
SET NAMES utf8mb4;

DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(80)  NOT NULL,
  email       VARCHAR(120) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,                 -- hash bcrypt (nunca texto plano)
  rol         VARCHAR(10)  NOT NULL DEFAULT 'usuario',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_usuarios_rol CHECK (rol IN ('admin', 'usuario'))
) ENGINE=InnoDB;

-- Administrador inicial (email: admin@tanianailsart.com / contraseña: Admin1234)
-- La contraseña está guardada como hash generado con password_hash().
INSERT INTO usuarios (nombre, email, password, rol) VALUES
  ('Administradora', 'admin@tanianailsart.com', '$2y$10$GRMZ17QsyRSEAzknJbBa2OqaXqQsXWcu4sDsNEztB.7UHHt5By6gu', 'admin');
