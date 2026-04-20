<?php
/**
 * bienvenida_colaborador.php — Email de bienvenida al miembro del equipo
 * tras completar su registro en formulario_datos_colaborador.php.
 *
 * Variables:
 *   $nombre    string       Primer nombre del colaborador
 *   $empresa   string       Nombre de la empresa/marca
 *   $logo      string|null  URL del logo de la empresa
 */

$dashboard_url = 'https://www.valirica.com/app.valirica.com/login_equipo.php';

$titulo    = '¡Bienvenido/a a ' . $empresa . ' en Valírica!';
$preheader = 'Ya eres parte de ' . $empresa . ' en Valírica. Vale te da la bienvenida.';

ob_start();
?>

<p style="margin:0 0 20px;font-size:22px;font-weight:700;color:#0d0d0d;line-height:1.3;letter-spacing:-.2px;">
  ¡Hola, <?= htmlspecialchars($nombre) ?>!
</p>

<p style="margin:0 0 18px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Ya formas parte de <strong><?= htmlspecialchars($empresa) ?></strong> en Valírica.
  Gracias por completar tu registro — a partir de ahora tienes un espacio propio
  donde podrás ver tu perfil, tus solicitudes y todo lo que el equipo comparte contigo.
</p>

<p style="margin:0 0 32px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Las organizaciones no se construyen solas — son el reflejo del esfuerzo, la energía
  y el talento de cada persona que las forma. Tú eres parte esencial del desarrollo
  y la cultura de <strong><?= htmlspecialchars($empresa) ?></strong>. Nunca lo olvides. 🧡
</p>

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 40px;">
  <tr>
    <td style="border-radius:10px;background-color:#EF7F1B;">
      <a href="<?= htmlspecialchars($dashboard_url) ?>"
         style="display:inline-block;padding:13px 28px;font-size:15px;font-weight:600;
                color:#ffffff;text-decoration:none;letter-spacing:.1px;">
        Ver mi espacio en Valírica →
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
