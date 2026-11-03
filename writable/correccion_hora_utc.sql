-- Corrección de marcas guardadas en hora UTC (antes de fijar America/Lima).
-- Solo registros reales hechos desde el panel; los datos de prueba no se tocan.
-- Comprobado con la foto de evidencia de la marca #96: archivo creado 12:58:48, BD decía 17:58:48 (−5 h).

START TRANSACTION;

-- Respaldo de las filas antes del cambio
CREATE TABLE IF NOT EXISTS _bak_attendance_utc AS SELECT * FROM attendance WHERE id IN (70, 96);
CREATE TABLE IF NOT EXISTS _bak_incidents_utc  AS SELECT * FROM incidents  WHERE id IN (9, 18, 25);

-- Marcas: entrada y salida −5 horas (el estado sigue siendo tardanza)
UPDATE attendance
   SET time_in  = SUBTIME(time_in,  '05:00:00'),
       time_out = IF(time_out IS NULL, NULL, SUBTIME(time_out, '05:00:00'))
 WHERE id IN (70, 96);

-- Incidencias: fecha y fecha de resolución −5 horas
UPDATE incidents
   SET fecha       = DATE_SUB(fecha, INTERVAL 5 HOUR),
       resuelta_en = IF(resuelta_en IS NULL, NULL, DATE_SUB(resuelta_en, INTERVAL 5 HOUR))
 WHERE id IN (9, 18, 25);

-- Texto de las tardanzas con la hora real
UPDATE incidents SET detalle = 'Llegó a las 14:52' WHERE id = 9  AND detalle = 'Llegó a las 19:52';
UPDATE incidents SET detalle = 'Llegó a las 12:58' WHERE id = 25 AND detalle = 'Llegó a las 17:58';

COMMIT;
