-- Datos de prueba para el CRUD (ejecutar después de schema.sql)
USE tanianailsart;
SET NAMES utf8mb4;   -- asegura que las tildes y la ñ se guarden bien

-- 1.a Tablas maestras
INSERT INTO roles (nombre) VALUES ('administrador'), ('clienta');

INSERT INTO categorias_servicio (nombre) VALUES
  ('Esculpidas'),
  ('Gel'),
  ('Diseño');

INSERT INTO estados_turno (nombre) VALUES
  ('pendiente'),
  ('confirmado'),
  ('cancelado'),
  ('completado');

-- 1.b Usuarios (la contraseña es un hash de ejemplo, no texto plano)
INSERT INTO usuarios (nombre, apellido, email, password_hash, telefono, rol_id) VALUES
  ('Tania',  'Gómez',     'tania@tanianailsart.com', '$2y$10$hashDeEjemploAdmin00000000000000000000000000000000', '3875550001', 1),
  ('Sofía',  'Rodríguez', 'sofia@mail.com',          '$2y$10$hashDeEjemploClienta0000000000000000000000000000000', '3875550002', 2),
  ('Lucía',  'Pérez',     'lucia@mail.com',          '$2y$10$hashDeEjemploClienta0000000000000000000000000000001', '3875550003', 2);

-- 1.c Servicios (vinculados a su categoría por FK)
INSERT INTO servicios (nombre, descripcion, precio, duracion_min, categoria_id) VALUES
  ('Uñas esculpidas',  'Estructura en gel con molde',        18000, 120, 1),
  ('Kapping gel',      'Gel sobre uña natural',              12000,  90, 2),
  ('Diseño artístico', 'Decoración a mano alzada',            8000,  45, 3),
  ('Uñas 3D',          'Relieves y figuras en 3D',           15000,  75, 3);

-- 1.d Turnos (tabla principal, vinculando usuario y estado por FK)
INSERT INTO turnos (usuario_id, estado_id, fecha_hora, notas) VALUES
  (2, 1, '2026-11-05 10:00:00', 'Primera vez, quiere tonos rosados'),
  (3, 2, '2026-11-05 15:30:00', 'Viene con foto de referencia');

-- 1.e Detalle: qué servicios incluye cada turno
INSERT INTO detalle_turno (turno_id, servicio_id, precio_aplicado) VALUES
  (1, 1, 18000),
  (1, 3,  8000),
  (2, 2, 12000);

