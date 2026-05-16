-- =====================================================
-- Script de Correcciones BD - GIBI-ITSI
-- Ejecutar en phpMyAdmin o MySQL CLI
-- =====================================================

-- 1. Corregir vista v_estadisticas_sistema (roles invertidos)
-- Actualmente: total_estudiantes cuenta rol_id=2 (Admin), total_superadmin cuenta rol_id=1 (Estudiante)
-- Corrección: rol_id=1=Estudiante, rol_id=2=Admin Bienestar, rol_id=4=Super Admin

DROP VIEW IF EXISTS `v_estadisticas_sistema`;
CREATE VIEW `v_estadisticas_sistema` AS 
SELECT 
    (SELECT COUNT(0) FROM `usuarios` WHERE `usuarios`.`rol_id` = 1) AS `total_estudiantes`,
    (SELECT COUNT(0) FROM `usuarios` WHERE `usuarios`.`rol_id` = 2) AS `total_admin_bienestar`,
    (SELECT COUNT(0) FROM `usuarios` WHERE `usuarios`.`rol_id` = 4) AS `total_superadmin`,
    (SELECT COUNT(0) FROM `periodos_academicos` WHERE `periodos_academicos`.`activo` = 1) AS `periodos_activos`,
    (SELECT COUNT(0) FROM `fichas_socioeconomicas` WHERE `fichas_socioeconomicas`.`estado` = 'Finalizada') AS `fichas_completadas`,
    (SELECT COUNT(0) FROM `solicitudes_becas` WHERE `solicitudes_becas`.`estado` = 'Pendiente') AS `becas_pendientes`,
    (SELECT COUNT(0) FROM `solicitudes_ayuda_mejorada` WHERE `solicitudes_ayuda_mejorada`.`estado` = 'Pendiente') AS `ayudas_pendientes`;

-- 2. Normalizar tabla de roles para coincidir con el código
-- El código usa: 1=Estudiante, 2=Admin Bienestar, 4=Super Admin
UPDATE `roles` SET `nombre` = 'Estudiante', `descripcion` = 'Estudiante del instituto' WHERE `id` = 1;
UPDATE `roles` SET `nombre` = 'Admin Bienestar', `descripcion` = 'Administrador de Bienestar Estudiantil' WHERE `id` = 2;
UPDATE `roles` SET `nombre` = 'Docente', `descripcion` = 'Docente del instituto' WHERE `id` = 3;
UPDATE `roles` SET `nombre` = 'Super Administrador', `descripcion` = 'Administrador global del sistema' WHERE `id` = 4;

-- 3. Corregir usuario superadmin (id=8) si su rol_id no es 4
UPDATE `usuarios` SET `rol_id` = 4 WHERE `id` = 8 AND `cedula` = '0004';

-- 4. Limpiar datos de prueba en solicitudes_ayuda (opcional - descomentar si deseas ejecutar)
-- DELETE FROM `solicitudes_ayuda` WHERE `descripcion` IN ('ASDSADQWQE', 'DSADADSADASD');

-- 5. Verificación post-corrección
SELECT 'Verificación de roles:' AS info;
SELECT r.id, r.nombre, COUNT(u.id) as total_usuarios
FROM roles r
LEFT JOIN usuarios u ON u.rol_id = r.id
GROUP BY r.id, r.nombre
ORDER BY r.id;

SELECT 'Vista corregida:' AS info;
SELECT * FROM v_estadisticas_sistema;
