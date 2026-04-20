<?php
/**
 * Migration: Geo-Fichaje — Fase 1
 * Ejecutar una sola vez desde el navegador (admin autenticado).
 * Eliminar este archivo después de confirmar los resultados.
 */
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die('Acceso no autorizado.');
}

$migrations = [];

// ─── TABLA: turnos — nuevas columnas geo-fichaje ──────────────────────────────

$migrations['turnos_modalidad'] = "
    ALTER TABLE turnos
    ADD COLUMN modalidad ENUM('presencial','remoto','hibrido') NOT NULL DEFAULT 'presencial'
    AFTER cruza_medianoche
";

$migrations['turnos_requiere_geo'] = "
    ALTER TABLE turnos
    ADD COLUMN requiere_geo TINYINT(1) NOT NULL DEFAULT 0
    AFTER modalidad
";

$migrations['turnos_geo_nombre_lugar'] = "
    ALTER TABLE turnos
    ADD COLUMN geo_nombre_lugar VARCHAR(100) DEFAULT NULL
    AFTER requiere_geo
";

$migrations['turnos_geo_lat'] = "
    ALTER TABLE turnos
    ADD COLUMN geo_lat DECIMAL(10,7) DEFAULT NULL
    AFTER geo_nombre_lugar
";

$migrations['turnos_geo_lng'] = "
    ALTER TABLE turnos
    ADD COLUMN geo_lng DECIMAL(10,7) DEFAULT NULL
    AFTER geo_lat
";

$migrations['turnos_geo_radio_metros'] = "
    ALTER TABLE turnos
    ADD COLUMN geo_radio_metros INT NOT NULL DEFAULT 100
    AFTER geo_lng
";

$migrations['turnos_geo_modo_estricto'] = "
    ALTER TABLE turnos
    ADD COLUMN geo_modo_estricto TINYINT(1) NOT NULL DEFAULT 0
    AFTER geo_radio_metros
";

// ─── TABLA: asistencias — resultado de la verificación geo ───────────────────
// IMPORTANTE: Solo se guarda el resultado de la verificación (booleano + distancia
// en metros), NUNCA las coordenadas GPS del empleado. Esto cumple con las
// recomendaciones de la AEPD (España) y el principio de minimización de datos
// de la Ley 1581 (Colombia).

$migrations['asistencias_geo_verificado_entrada'] = "
    ALTER TABLE asistencias
    ADD COLUMN geo_verificado_entrada TINYINT(1) DEFAULT NULL
    COMMENT '1=dentro del perímetro, 0=fuera, NULL=no aplica o sin verificación'
";

$migrations['asistencias_geo_distancia_entrada_m'] = "
    ALTER TABLE asistencias
    ADD COLUMN geo_distancia_entrada_m INT DEFAULT NULL
    COMMENT 'Distancia aproximada en metros al centro del geofence al hacer entrada'
";

$migrations['asistencias_geo_verificado_salida'] = "
    ALTER TABLE asistencias
    ADD COLUMN geo_verificado_salida TINYINT(1) DEFAULT NULL
    COMMENT '1=dentro del perímetro, 0=fuera, NULL=no aplica o sin verificación'
";

$migrations['asistencias_geo_distancia_salida_m'] = "
    ALTER TABLE asistencias
    ADD COLUMN geo_distancia_salida_m INT DEFAULT NULL
    COMMENT 'Distancia aproximada en metros al centro del geofence al hacer salida'
";

// ─── TABLA: lugares_trabajo (nueva) ──────────────────────────────────────────
// Permite al admin guardar ubicaciones reutilizables para asignar a turnos.

$migrations['create_lugares_trabajo'] = "
    CREATE TABLE lugares_trabajo (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id   INT NOT NULL,
        nombre       VARCHAR(100) NOT NULL,
        lat          DECIMAL(10,7) NOT NULL,
        lng          DECIMAL(10,7) NOT NULL,
        radio_metros INT NOT NULL DEFAULT 100,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_lug_usuario (usuario_id),
        CONSTRAINT fk_lug_usuario
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

// ─── Ejecutar ─────────────────────────────────────────────────────────────────

$results = [];
foreach ($migrations as $name => $sql) {
    $sql = trim($sql);
    $ok  = $conn->query($sql);
    if ($ok) {
        $results[$name] = 'OK';
    } else {
        $errno = $conn->errno;
        $error = $conn->error;
        // 1060 = columna duplicada · 1050 = tabla ya existe · 1091 = índice no existe
        if (in_array($errno, [1060, 1050, 1091])) {
            $results[$name] = "SKIP (ya existe: $error)";
        } else {
            $results[$name] = "ERROR $errno: $error";
        }
    }
}

header('Content-Type: text/plain; charset=utf-8');
echo "=== VALIRICA — Migración Geo-Fichaje Fase 1 ===\n\n";
foreach ($results as $name => $status) {
    $icon = str_starts_with($status, 'OK')    ? '✅'
          : (str_starts_with($status, 'SKIP') ? '⚠️' : '❌');
    echo "$icon  $name: $status\n";
}

$errores = array_filter($results, fn($s) => str_starts_with($s, 'ERROR'));
echo "\n";
echo empty($errores)
    ? "✅ Migración completada sin errores críticos.\n"
    : "❌ " . count($errores) . " error(s) encontrado(s). Revisar arriba.\n";
echo "Elimina este archivo después de confirmar.\n";
