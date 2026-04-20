<?php
/**
 * nueva_solicitud.php — Notificación al admin cuando un empleado solicita
 * permiso, vacaciones o registra una jornada fuera de horario.
 *
 * Variables:
 *   $nombre_admin    string  Primer nombre del admin (para el saludo)
 *   $nombre_empleado string  Nombre completo del empleado
 *   $tipo            string  'permiso' | 'vacaciones' | 'jornada extra'
 *   $fechas          string  Rango de fechas o descripción del período
 *   $dashboard_url   string  URL al dashboard del admin
 */

$tipo_labels = [
    'permiso'       => 'un permiso',
    'vacaciones'    => 'vacaciones',
    'jornada extra' => 'trabajo fuera de jornada',
];
$tipo_label = $tipo_labels[$tipo] ?? $tipo;

$titulo    = $nombre_empleado . ' tiene una solicitud pendiente';
$preheader = $nombre_empleado . ' ha registrado una solicitud de ' . $tipo_label . ' y espera tu aprobación.';

ob_start();
?>

<p style="margin:0 0 20px;font-size:22px;font-weight:700;color:#0d0d0d;line-height:1.3;letter-spacing:-.2px;">
  ¡Hola, <?= htmlspecialchars($nombre_admin) ?>!
</p>

<p style="margin:0 0 18px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  <strong><?= htmlspecialchars($nombre_empleado) ?></strong>
  ha registrado una solicitud de <strong><?= htmlspecialchars($tipo_label) ?></strong>
  <?php if (!empty($fechas)): ?>
    para el período <strong><?= htmlspecialchars($fechas) ?></strong>
  <?php endif; ?>
  y espera tu aprobación.
</p>

<p style="margin:0 0 32px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Puedes revisarla y tomar una decisión directamente desde tu dashboard.
</p>

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 40px;">
  <tr>
    <td style="border-radius:10px;background-color:#EF7F1B;">
      <a href="<?= htmlspecialchars($dashboard_url) ?>"
         style="display:inline-block;padding:13px 28px;font-size:15px;font-weight:600;
                color:#ffffff;text-decoration:none;letter-spacing:.1px;">
        Ver solicitud en el dashboard →
      </a>
    </td>
  </tr>
</table>

<hr style="border:none;border-top:1px solid #eeecea;margin:0 0 28px;" />

<!-- Firma de Vale -->
<p style="margin:0;font-size:14px;color:#555;line-height:1.7;">
  Mi nombre es <strong style="color:#0d0d0d;">Vale</strong>, la
  <strong style="color:#0d0d0d;">CIA</strong>
  <span style="color:#888;font-style:italic;">(Cultural Intelligence Agent)</span>
  de Valírica. Escríbeme cuando quieras a
  <a href="mailto:vale@valirica.com"
     style="color:#EF7F1B;text-decoration:none;font-weight:500;">vale@valirica.com</a>
   — estoy aquí. 🧡
</p>

<?php
$contenido = ob_get_clean();
include __DIR__ . '/base.php';
