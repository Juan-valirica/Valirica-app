<?php
/**
 * nuevo_colaborador.php — Notificación al admin cuando un colaborador
 * completa su registro.
 *
 * Variables:
 *   $nombre_admin      string  Primer nombre del admin
 *   $nombre_colaborador string  Nombre completo del colaborador
 *   $empresa           string  Nombre de la empresa
 *   $dashboard_url     string  URL al dashboard del admin
 */

$titulo    = $nombre_colaborador . ' ha iniciado su registro';
$preheader = $nombre_colaborador . ' ha comenzado su registro en Valírica.';

ob_start();
?>

<p style="margin:0 0 20px;font-size:22px;font-weight:700;color:#0d0d0d;line-height:1.3;letter-spacing:-.2px;">
  ¡Hola, <?= htmlspecialchars($nombre_admin) ?>!
</p>

<p style="margin:0 0 18px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  <strong><?= htmlspecialchars($nombre_colaborador) ?></strong> ha iniciado su registro
  en <strong><?= htmlspecialchars($empresa) ?></strong> en Valírica.
  Ya registró su información personal y está completando su perfil.
</p>

<p style="margin:0 0 32px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Puedes ver su perfil y gestionar su información desde tu dashboard.
</p>

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 40px;">
  <tr>
    <td style="border-radius:10px;background-color:#EF7F1B;">
      <a href="<?= htmlspecialchars($dashboard_url) ?>"
         style="display:inline-block;padding:13px 28px;font-size:15px;font-weight:600;
                color:#ffffff;text-decoration:none;letter-spacing:.1px;">
        Ver mi equipo →
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