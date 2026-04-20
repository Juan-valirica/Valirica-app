<?php
/**
 * bienvenida.php — Email de bienvenida tras completar cultura_ideal.php
 *
 * Variables:
 *   $nombre     string       Primer nombre del usuario
 *   $empresa    string       Nombre de la empresa/marca
 *   $logo       string|null  URL del logo de la empresa
 *   $proposito  string|null  Propósito/misión de la empresa
 */

$dashboard_url = 'https://www.valirica.com/app.valirica.com/login.php';

// Truncar propósito a ~130 chars para que fluya bien en el párrafo
$proposito_inline = null;
if (!empty($proposito)) {
    $p = trim($proposito);
    $proposito_inline = mb_strlen($p) > 130 ? mb_substr($p, 0, 127) . '…' : $p;
}

$titulo    = '¡Bienvenido/a a Valírica, ' . $nombre . '!';
$preheader = 'Un espacio para acercarte cada vez más al propósito de ' . $empresa . '. Vale te da la bienvenida.';

ob_start();
?>

<p style="margin:0 0 20px;font-size:22px;font-weight:700;color:#0d0d0d;line-height:1.3;letter-spacing:-.2px;">
  ¡Hola, <?= htmlspecialchars($nombre) ?>!
</p>

<p style="margin:0 0 18px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Te damos la bienvenida a Valírica — un espacio para gestionar de manera estratégica
  el talento de <strong><?= htmlspecialchars($empresa) ?></strong> y ayudarte a acercarte
  cada vez más<?php if ($proposito_inline): ?> a ese gran propósito:
  <em style="color:#555;">"<?= htmlspecialchars($proposito_inline) ?>"</em><?php else: ?>
  a tus metas como organización<?php endif; ?>.
</p>

<p style="margin:0 0 32px;font-size:15px;color:#3d3c3b;line-height:1.7;">
  Desde este momento puedes acceder a tu dashboard para invitar y analizar a tu equipo,
  y usar todos los recursos que tenemos para ti.
</p>

<!-- CTA -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 40px;">
  <tr>
    <td style="border-radius:10px;background-color:#EF7F1B;">
      <a href="<?= htmlspecialchars($dashboard_url) ?>"
         style="display:inline-block;padding:13px 28px;font-size:15px;font-weight:600;
                color:#ffffff;text-decoration:none;letter-spacing:.1px;">
        Ir a mi dashboard →
      </a>
    </td>
  </tr>
</table>

<hr style="border:none;border-top:1px solid #eeecea;margin:0 0 28px;" />

<!-- Presentación de Vale -->
<p style="margin:0 0 16px;font-size:14px;color:#555;line-height:1.7;">
  Mi nombre es <strong style="color:#0d0d0d;">Vale</strong>, soy la
  <strong style="color:#0d0d0d;">CIA</strong>
  <span style="color:#888;font-style:italic;">(Cultural Intelligence Agent)</span>
  de Valírica, y estaré encantada de acompañarte y contestar todas tus dudas
  e inquietudes — o simplemente escucharte cada vez que quieras contarnos algo
  o enviarnos un saludo.
</p>

<p style="margin:0 0 24px;font-size:14px;color:#555;line-height:1.7;">
  Escríbeme cuando quieras a
  <a href="mailto:vale@valirica.com"
     style="color:#EF7F1B;text-decoration:none;font-weight:500;">vale@valirica.com</a>
   — estoy aquí. 🧡
</p>

<!-- Firma de Vale -->
<p style="margin:0;font-size:13px;color:#888;line-height:1.5;">
  <strong style="color:#3d3c3b;">Vale</strong><br />
  <span style="color:#a09e9c;">Cultural Intelligence Agent · Valírica</span>
</p>

<?php
$contenido = ob_get_clean();
include __DIR__ . '/base.php';
