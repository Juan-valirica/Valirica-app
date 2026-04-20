<?php
/**
 * base.php — Layout HTML base para todos los emails de Valírica.
 *
 * Variables esperadas:
 *   $titulo        string  Título del email (aparece en el <title>)
 *   $preheader     string  Texto de previsualización en el cliente de email
 *   $contenido     string  HTML del cuerpo principal (ya renderizado)
 */
?>
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title><?= htmlspecialchars($titulo ?? 'Valírica') ?></title>
  <style>
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td          { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img                { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    body               { margin: 0 !important; padding: 0 !important; background-color: #f0eeeb; }
    a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
    @media only screen and (max-width: 600px) {
      .wrapper { width: 100% !important; }
      .inner   { padding: 36px 28px 32px !important; }
      .foot    { padding: 24px 28px 32px !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#f0eeeb;
             font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Helvetica Neue',Arial,sans-serif;">

  <!-- Preheader invisible -->
  <div style="display:none;font-size:1px;color:#f0eeeb;line-height:1px;
              max-height:0;max-width:0;opacity:0;overflow:hidden;">
    <?= htmlspecialchars($preheader ?? '') ?>&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
  </div>

  <!-- ── Wrapper ──────────────────────────────────────────────── -->
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
         style="background-color:#f0eeeb;">
    <tr>
      <td align="center" style="padding:44px 16px 52px;">

        <!-- ── Card ─────────────────────────────────────────── -->
        <table role="presentation" class="wrapper" width="600" cellpadding="0" cellspacing="0" border="0"
               style="max-width:600px;width:100%;background:#ffffff;
                      border-radius:16px;border:1px solid #e4e2df;
                      box-shadow:0 2px 16px rgba(1,33,51,.06);">

          <!-- Accent bar — señal de marca sin ruido visual -->
          <tr>
            <td style="background-color:#EF7F1B;height:3px;font-size:0;line-height:0;
                       border-radius:16px 16px 0 0;">&nbsp;</td>
          </tr>

          <!-- Contenido principal -->
          <tr>
            <td class="inner" style="padding:48px 52px 40px;">
              <?= $contenido ?>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td class="foot" style="padding:24px 52px 36px;border-top:1px solid #f0eeeb;">

              <!-- Logo sin contenedor — firma minimalista -->
              <img src="https://valirica.com/uploads/logo-valirica.png"
                   alt="Valírica" width="110" height="auto"
                   style="display:block;max-width:110px;margin:0 0 14px;" />

              <p style="margin:0;font-size:13px;color:#a09e9c;line-height:1.7;
                        font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Helvetica Neue',Arial,sans-serif;">
                Puedes responder este correo en cualquier momento —
                o escribirnos directamente a
                <a href="mailto:vale@valirica.com"
                   style="color:#EF7F1B;text-decoration:none;">vale@valirica.com</a>.
                Estamos aquí para lo que necesites, o simplemente para saludar.
              </p>

              <p style="margin:12px 0 0;font-size:11px;color:#c4c2bf;line-height:1.5;
                        font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Helvetica Neue',Arial,sans-serif;">
                © <?= date('Y') ?> Valírica · Este mensaje fue generado automáticamente.
              </p>

            </td>
          </tr>

        </table>
        <!-- /Card -->

      </td>
    </tr>
  </table>
  <!-- /Wrapper -->

</body>
</html>