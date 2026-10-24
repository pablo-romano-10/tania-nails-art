-- ============================================================
-- TP6 - Consultas de prueba CRUD
-- Ejecutar DESPUÉS de schema.sql
-- ============================================================
USE tanianailsart;

-- ============================================================
-- CREATE (insertar datos)
-- ============================================================

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

-- ============================================================
-- READ (consultas)
-- ============================================================

-- 2.a SELECT con WHERE y ORDER BY:
--     servicios activos de más de $10.000, del más caro al más barato
SELECT id, nombre, precio, duracion_min
FROM servicios
WHERE activo = TRUE AND precio > 10000
ORDER BY precio DESC;

-- 2.b INNER JOIN de varias tablas:
--     agenda con clienta, estado, servicio, categoría y precio
SELECT
  t.id                           AS turno,
  t.fecha_hora,
  CONCAT(u.nombre, ' ', u.apellido) AS clienta,
  e.nombre                       AS estado,
  s.nombre                       AS servicio,
  c.nombre                       AS categoria,
  d.precio_aplicado
FROM turnos t
INNER JOIN usuarios            u ON u.id = t.usuario_id
INNER JOIN estados_turno       e ON e.id = t.estado_id
INNER JOIN detalle_turno       d ON d.turno_id = t.id
INNER JOIN servicios           s ON s.id = d.servicio_id
INNER JOIN categorias_servicio c ON c.id = s.categoria_id
ORDER BY t.fecha_hora, s.nombre;

-- 2.c Total a cobrar por turno (JOIN + agregación)
SELECT t.id AS turno, SUM(d.precio_aplicado) AS total
FROM turnos t
INNER JOIN detalle_turno d ON d.turno_id = t.id
GROUP BY t.id;

-- ============================================================
-- UPDATE (siempre con WHERE)
-- ============================================================

-- 3. Confirmar el turno 1 (pasa de 'pendiente' a 'confirmado')
UPDATE turnos
SET estado_id = (SELECT id FROM estados_turno WHERE nombre = 'confirmado')
WHERE id = 1;

-- Verificación
SELECT t.id, e.nombre AS estado
FROM turnos t
INNER JOIN estados_turno e ON e.id = t.estado_id
WHERE t.id = 1;

-- ============================================================
-- DELETE (siempre con WHERE)
-- ============================================================

-- 4. Dar de baja el turno 2 (el detalle se borra solo por ON DELETE CASCADE)
DELETE FROM turnos
WHERE id = 2;

-- Verificación
SELECT id, fecha_hora FROM turnos;
