-- ============================================================
-- TP6 - Esquema relacional MySQL
-- Proyecto: Tanianailsart (sistema de turnos de manicuría)
-- Ejecutar completo en phpMyAdmin / XAMPP / Laragon / WampServer
-- ============================================================

-- ------------------------------------------------------------
-- 1. BASE DE DATOS
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS tanianailsart
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE tanianailsart;
SET NAMES utf8mb4;   -- asegura que las tildes y la ñ se guarden bien

-- Para poder volver a ejecutar el script sin errores:
-- se borran primero las tablas hijas y luego las padres.
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS detalle_turno;
DROP TABLE IF EXISTS turnos;
DROP TABLE IF EXISTS servicios;
DROP TABLE IF EXISTS categorias_servicio;
DROP TABLE IF EXISTS estados_turno;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 2. TABLAS MAESTRAS / SECUNDARIAS
-- ------------------------------------------------------------

-- Roles de acceso al sistema (administrador, clienta)
CREATE TABLE roles (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(30) NOT NULL UNIQUE,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categorías de servicio (esculpidas, gel, diseño...)
CREATE TABLE categorias_servicio (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(50) NOT NULL UNIQUE,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Estados posibles de un turno (pendiente, confirmado, cancelado...)
CREATE TABLE estados_turno (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(30) NOT NULL UNIQUE,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. USUARIOS (autenticación + datos personales + rol)
-- ------------------------------------------------------------
CREATE TABLE usuarios (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nombre         VARCHAR(60)  NOT NULL,
  apellido       VARCHAR(60)  NOT NULL,
  email          VARCHAR(120) NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,          -- hash (password_hash de PHP), nunca texto plano
  telefono       VARCHAR(20),
  rol_id         INT NOT NULL,
  activo         BOOLEAN NOT NULL DEFAULT TRUE,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuarios_rol
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. CATÁLOGO DE SERVICIOS
-- ------------------------------------------------------------
CREATE TABLE servicios (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(80)   NOT NULL,
  descripcion   VARCHAR(255),
  precio        DECIMAL(10,2) NOT NULL CHECK (precio >= 0),
  duracion_min  INT           NOT NULL DEFAULT 60,
  categoria_id  INT           NOT NULL,
  activo        BOOLEAN       NOT NULL DEFAULT TRUE,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_servicios_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias_servicio(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. TABLA PRINCIPAL DE NEGOCIO: TURNOS
-- ------------------------------------------------------------
CREATE TABLE turnos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT NOT NULL,
  estado_id   INT NOT NULL,
  fecha_hora  DATETIME NOT NULL,
  notas       TEXT,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_turnos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  CONSTRAINT fk_turnos_estado
    FOREIGN KEY (estado_id) REFERENCES estados_turno(id),
  -- Evita dos turnos en el mismo horario
  CONSTRAINT uq_turnos_fecha_hora UNIQUE (fecha_hora)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. TABLA DE DETALLE (relación N:M turnos <-> servicios)
-- Un turno puede incluir varios servicios y un servicio
-- aparece en muchos turnos.
-- ------------------------------------------------------------
CREATE TABLE detalle_turno (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  turno_id         INT NOT NULL,
  servicio_id      INT NOT NULL,
  precio_aplicado  DECIMAL(10,2) NOT NULL,   -- precio al momento de reservar (histórico)
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_detalle_turno
    FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE CASCADE,
  CONSTRAINT fk_detalle_servicio
    FOREIGN KEY (servicio_id) REFERENCES servicios(id),
  -- No repetir el mismo servicio dentro de un mismo turno
  CONSTRAINT uq_detalle UNIQUE (turno_id, servicio_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 7. ÍNDICES DE RENDIMIENTO
-- ------------------------------------------------------------
-- Filtrar/ordenar turnos por fecha (agenda diaria o semanal)
CREATE INDEX idx_turnos_fecha_hora  ON turnos (fecha_hora);
-- Listar los servicios de una categoría
CREATE INDEX idx_servicios_categoria ON servicios (categoria_id, activo);
-- Buscar los turnos de una clienta
CREATE INDEX idx_turnos_usuario      ON turnos (usuario_id);
-- (usuarios.email ya tiene índice por su restricción UNIQUE)
