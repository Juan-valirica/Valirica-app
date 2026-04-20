<?php
/**
 * aprobacion.php — Notificación al empleado cuando su solicitud es aprobada o rechazada.
 *
 * Variables:
 *   $nombre   string       Primer nombre del empleado
 *   $tipo     string       'permiso' | 'vacaciones' | 'jornada extra'
 *   $estado   string       'aprobado' | 'rechazado'
 *   $fechas   string       Rango de fechas o descripción del período
 *   $motivo   string|null  Motivo del rechazo (solo si $estado === 'rechazado')
 */

$aprobado = $estado === 'aprobado';

$tipo_labels = [
    'permiso'       => 'tu permiso',
    'vacaciones'    => 'tus vacaciones',
    'jornada extra' => 'tu jornada fuera de horario',
];
$tipo_label = $tipo_labels[$tipo] ?? ('tu solicitud de ' . $tipo);

$titulo    = $aprobado
    ? '¡Tu solicitud fue aprobada!'
    : 'Actualización sobre tu solicitud';
$preheader = $aprobado
    ? ucfirst($tipo_label) . ' ha sido aprobada. ¡Todo listo!'
    : ucfirst($tipo_label) . ' no pudo ser aprobada. Te compartimos el motivo.';

ob_start();
?>

<p style="margin:0 0 20px;font-size:22px;font-weight:700;color:#0d0d0d;line-height:1.3;letter-spacing:-.2px;">
  ¡Hola, <?= htmlspecialchars($nombre) ?>!
</p>

<?php if ($aprobado): ?>

<p style="margin:0 0 18px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Buenas noticias — <?= htmlspecialchars($tipo_label) ?>
  <?php if (!empty($fechas)): ?>
    del período <strong><?= htmlspecialchars($fechas) ?></strong>
  <?php endif; ?>
  ha sido <strong style="color:#16a34a;">aprobada ✓</strong>.
</p>

<p style="margin:0 0 32px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Puedes consultar el estado de todas tus solicitudes en cualquier momento desde tu dashboard.
</p>

<?php else: ?>

<p style="margin:0 0 18px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Queríamos contarte que <?= htmlspecialchars($tipo_label) ?>
  <?php if (!empty($fechas)): ?>
    del período <strong><?= htmlspecialchars($fechas) ?></strong>
  <?php endif; ?>
  no ha podido ser aprobada en esta ocasión.
</p>

<?php if (!empty($motivo)): ?>
<div style="margin:0 0 28px;padding:16px 20px;background:#fdf2f2;border-left:3px solid #dc2626;border-radius:0 8px 8px 0;">
  <p style="margin:0;font-size:14px;color:#7f1d1d;line-height:1.6;">
    <strong>Motivo:</strong> <?= htmlspecialchars($motivo) ?>
  </p>
</div>
<?php endif; ?>

<p style="margin:0 0 32px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Si tienes alguna duda o quieres hablar con tu responsable, no dudes en escribirle directamente.
</p>

<?php endif; ?>

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 40px;">
  <tr>
    <td style="border-radius:10px;background-color:#EF7F1B;">
      <a href="https://www.valirica.com/app.valirica.com/login_equipo.php"
         style="display:inline-block;padding:13px 28px;font-size:15px;font-weight:600;
                color:#ffffff;text-decoration:none;letter-spacing:.1px;">
        Ver mis solicitudes →
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
