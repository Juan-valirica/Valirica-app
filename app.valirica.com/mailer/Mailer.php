<?php
/**
 * Mailer — Servicio central de emails para Valírica
 * Usa Amazon SES v2 vía API HTTPS (el hosting bloquea puertos SMTP salientes).
 *
 * Uso:
 *   require_once __DIR__ . '/../vendor/autoload.php';
 *   $ok = Mailer::sendBienvenida($nombre, $email);
 *   $ok = Mailer::sendInvitacion($emailDestino, $inviteUrl, $empresaNombre);
 */

use Aws\SesV2\SesV2Client;
use Aws\Exception\AwsException;

class Mailer
{
    // -------------------------------------------------------------------------
    // Método base: devuelve un SesV2Client configurado
    // -------------------------------------------------------------------------
    private static function build(): SesV2Client
    {
        return new SesV2Client([
            'version'     => 'latest',
            'region'      => AWS_REGION,
            'credentials' => [
                'key'    => AWS_ACCESS_KEY_ID,
                'secret' => AWS_SECRET_ACCESS_KEY,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Renderiza un template PHP pasándole variables
    // -------------------------------------------------------------------------
    private static function renderTemplate(string $template, array $vars = []): string
    {
        $path = __DIR__ . '/templates/' . $template . '.php';
        if (!file_exists($path)) {
            error_log("Mailer: template no encontrado: $path");
            return '';
        }
        extract($vars, EXTR_SKIP);
        ob_start();
        include $path;
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // Construye el array de destino para SES v2
    // -------------------------------------------------------------------------
    private static function buildDestination(string $toEmail, ?string $bcc = null): array
    {
        $dest = ['ToAddresses' => [$toEmail]];
        if ($bcc) {
            $dest['BccAddresses'] = [$bcc];
        }
        return $dest;
    }

    // -------------------------------------------------------------------------
    // Envío genérico — método interno
    // -------------------------------------------------------------------------
    private static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        return self::dispatch($toEmail, $toName, $subject, $htmlBody);
    }

    // -------------------------------------------------------------------------
    // Envío con BCC opcional
    // -------------------------------------------------------------------------
    private static function sendWithBcc(
        string  $toEmail,
        string  $toName,
        string  $subject,
        string  $htmlBody,
        ?string $bcc = null
    ): bool {
        return self::dispatch($toEmail, $toName, $subject, $htmlBody, $bcc);
    }

    // -------------------------------------------------------------------------
    // Despacho real vía AWS SES v2 API HTTPS
    // -------------------------------------------------------------------------
    private static function dispatch(
        string  $toEmail,
        string  $toName,
        string  $subject,
        string  $htmlBody,
        ?string $bcc = null
    ): bool {
        try {
            $client = self::build();

            $fromAddress = SES_FROM_NAME . ' <' . SES_FROM_EMAIL . '>';
            $altBody     = strip_tags(str_replace(
                ['<br>', '<br/>', '<br />', '</p>', '</li>'],
                "\n",
                $htmlBody
            ));

            $params = [
                'ConfigurationSetName' => SES_CONFIG_SET,
                'Destination'          => self::buildDestination($toEmail, $bcc),
                'FromEmailAddress'     => $fromAddress,
                'ReplyToAddresses'     => [SES_REPLY_TO],
                'Content'              => [
                    'Simple' => [
                        'Subject' => ['Data' => $subject, 'Charset' => 'UTF-8'],
                        'Body'    => [
                            'Html' => ['Data' => $htmlBody, 'Charset' => 'UTF-8'],
                            'Text' => ['Data' => $altBody,  'Charset' => 'UTF-8'],
                        ],
                    ],
                ],
            ];

            $client->sendEmail($params);
            return true;

        } catch (AwsException $e) {
            error_log("Mailer AWS error [{$toEmail}]: " . $e->getAwsErrorMessage());
            return false;
        } catch (\Throwable $e) {
            error_log("Mailer unexpected error [{$toEmail}]: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // EMAILS PÚBLICOS
    // =========================================================================

    /**
     * Email de bienvenida tras completar el formulario cultura_ideal.php.
     *
     * @param string      $nombre    Primer nombre del usuario
     * @param string      $email     Correo del usuario
     * @param string      $empresa   Nombre de la empresa/marca
     * @param string|null $logo      URL del logo de la empresa (null si no existe)
     * @param string|null $proposito Propósito/misión de la empresa (para personalizar)
     */
    public static function sendBienvenida(
        string  $nombre,
        string  $email,
        string  $empresa,
        ?string $logo      = null,
        ?string $proposito = null
    ): bool {
        $html = self::renderTemplate('bienvenida', [
            'nombre'    => $nombre,
            'empresa'   => $empresa,
            'logo'      => $logo,
            'proposito' => $proposito,
        ]);
        return self::send(
            $email,
            $nombre,
            'Todo listo, ' . $nombre . ' — tu espacio en Valírica está listo!',
            $html
        );
    }

    /**
     * Email de bienvenida al colaborador/empleado tras completar
     * formulario_datos_colaborador.php.
     *
     * @param string      $nombre   Primer nombre del colaborador
     * @param string      $email    Correo del colaborador
     * @param string      $empresa  Nombre de la empresa
     * @param string|null $logo     URL del logo de la empresa
     */
    public static function sendBienvenidaColaborador(
        string  $nombre,
        string  $email,
        string  $empresa,
        ?string $logo = null
    ): bool {
        $html = self::renderTemplate('bienvenida_colaborador', [
            'nombre'  => $nombre,
            'empresa' => $empresa,
            'logo'    => $logo,
        ]);
        return self::send(
            $email,
            $nombre,
            '¡Bienvenido/a a ' . $empresa . ' en Valírica!',
            $html
        );
    }

    /**
     * Notificación al admin cuando un colaborador completa su registro.
     *
     * @param string $emailAdmin         Correo del admin
     * @param string $nombreAdmin        Primer nombre del admin
     * @param string $nombreColaborador  Nombre completo del colaborador
     * @param string $empresa            Nombre de la empresa
     */
    public static function sendNuevoColaborador(
        string $emailAdmin,
        string $nombreAdmin,
        string $nombreColaborador,
        string $empresa
    ): bool {
        $html = self::renderTemplate('nuevo_colaborador', [
            'nombre_admin'       => $nombreAdmin,
            'nombre_colaborador' => $nombreColaborador,
            'empresa'            => $empresa,
            'dashboard_url'      => 'https://www.valirica.com/app.valirica.com/login.php',
        ]);
        return self::send(
            $emailAdmin,
            $nombreAdmin,
            $nombreColaborador . ' ha iniciado su registro en Valírica',
            $html
        );
    }

    /**
     * Notificación al admin cuando un colaborador completa todo el proceso.
     *
     * @param string $emailAdmin         Correo del admin
     * @param string $nombreAdmin        Primer nombre del admin
     * @param string $nombreColaborador  Nombre completo del colaborador
     * @param string $empresa            Nombre de la empresa
     */
    public static function sendColaboradorCompletado(
        string $emailAdmin,
        string $nombreAdmin,
        string $nombreColaborador,
        string $empresa
    ): bool {
        $html = self::renderTemplate('colaborador_completado', [
            'nombre_admin'       => $nombreAdmin,
            'nombre_colaborador' => $nombreColaborador,
            'empresa'            => $empresa,
            'dashboard_url'      => 'https://www.valirica.com/app.valirica.com/login.php',
        ]);
        return self::send(
            $emailAdmin,
            $nombreAdmin,
            $nombreColaborador . ' ha completado su perfil en Valírica',
            $html
        );
    }

    /**
     * Email de invitación para un nuevo miembro del equipo.
     *
     * @param string $emailDestino  Correo del invitado
     * @param string $inviteUrl     URL completa con token de invitación
     * @param string $empresaNombre Nombre de la empresa que invita
     */
    public static function sendInvitacion(string $emailDestino, string $inviteUrl, string $empresaNombre): bool
    {
        $html = self::renderTemplate('invitacion', [
            'empresa_nombre' => $empresaNombre,
            'invite_url'     => $inviteUrl,
        ]);
        return self::send(
            $emailDestino,
            '',
            $empresaNombre . ' te ha invitado a unirte a Valírica',
            $html
        );
    }

    /**
     * Notificación al empleado cuando se aprueba o rechaza su solicitud.
     *
     * @param string $nombre     Nombre del empleado
     * @param string $email      Correo del empleado
     * @param string $tipo       'permiso' | 'vacaciones'
     * @param string $estado     'aprobado' | 'rechazado'
     * @param string $fechas     Texto con las fechas (ej. "3 al 7 de marzo")
     */
    public static function sendAprobacion(
        string  $nombre,
        string  $email,
        string  $tipo,
        string  $estado,
        string  $fechas,
        ?string $motivo = null
    ): bool {
        $html = self::renderTemplate('aprobacion', [
            'nombre' => $nombre,
            'tipo'   => $tipo,
            'estado' => $estado,
            'fechas' => $fechas,
            'motivo' => $motivo,
        ]);

        $tipoLabels = [
            'permiso'       => 'permiso',
            'vacaciones'    => 'vacaciones',
            'jornada extra' => 'jornada fuera de horario',
        ];
        $tipoLabel   = $tipoLabels[$tipo] ?? $tipo;
        $estadoLabel = $estado === 'aprobado' ? 'aprobada ✓' : 'rechazada';
        $asunto = 'Tu solicitud de ' . $tipoLabel . ' ha sido ' . $estadoLabel;

        return self::send($email, $nombre, $asunto, $html);
    }

    // =========================================================================
    // CANAL DE DENUNCIAS
    // =========================================================================

    /**
     * Notificación al responsable cuando se recibe una nueva denuncia.
     *
     * @param string      $emailResponsable  Correo del responsable
     * @param string      $nombreResponsable Nombre del responsable
     * @param string      $referenceCode     Código VLD-YYYY-XXXX
     * @param string      $tipo              Tipo legible (ej. "Acoso laboral")
     * @param string      $country           'ES' | 'CO'
     * @param string      $manageUrl         URL al panel de gestión
     * @param string|null $bcc               BCC confidencial (null si no crítica)
     */
    public static function sendNuevaDenuncia(
        string  $emailResponsable,
        string  $nombreResponsable,
        string  $referenceCode,
        string  $tipo,
        string  $country,
        string  $manageUrl,
        ?string $bcc = null
    ): bool {
        $html = self::renderTemplate('denuncia_nueva', [
            'nombre_responsable' => $nombreResponsable,
            'reference_code'     => $referenceCode,
            'tipo'               => $tipo,
            'country'            => $country,
            'manage_url'         => $manageUrl,
        ]);
        return self::sendWithBcc(
            $emailResponsable,
            $nombreResponsable,
            "Nueva denuncia recibida — {$referenceCode}",
            $html,
            $bcc
        );
    }

    /**
     * Acuse de recibo al denunciante no anónimo.
     *
     * @param string $emailDenunciante Correo del denunciante
     * @param string $nombre           Nombre del denunciante
     * @param string $referenceCode    Código VLD-YYYY-XXXX
     * @param string $trackUrl         URL de seguimiento público
     * @param string $country          'ES' | 'CO'
     */
    public static function sendAcuseReciboDenuncia(
        string $emailDenunciante,
        string $nombre,
        string $referenceCode,
        string $trackUrl,
        string $country
    ): bool {
        $html = self::renderTemplate('denuncia_acuse', [
            'nombre'         => $nombre,
            'reference_code' => $referenceCode,
            'track_url'      => $trackUrl,
            'country'        => $country,
        ]);
        return self::send(
            $emailDenunciante,
            $nombre,
            "Hemos recibido tu denuncia — {$referenceCode}",
            $html
        );
    }

    /**
     * Actualización de estado al denunciante no anónimo.
     *
     * @param string $emailDenunciante Correo del denunciante
     * @param string $nombre           Nombre del denunciante
     * @param string $referenceCode    Código VLD-YYYY-XXXX
     * @param string $nuevoEstado      'en_tramite' | 'resuelta' | 'archivada'
     * @param string $trackUrl         URL de seguimiento público
     */
    public static function sendActualizacionDenuncia(
        string $emailDenunciante,
        string $nombre,
        string $referenceCode,
        string $nuevoEstado,
        string $trackUrl
    ): bool {
        $estado_labels = [
            'en_tramite' => 'En trámite',
            'resuelta'   => 'Resuelta',
            'archivada'  => 'Archivada',
        ];
        $estado_label = $estado_labels[$nuevoEstado] ?? ucfirst($nuevoEstado);

        $html = self::renderTemplate('denuncia_actualizacion', [
            'nombre'         => $nombre,
            'reference_code' => $referenceCode,
            'nuevo_estado'   => $nuevoEstado,
            'estado_label'   => $estado_label,
            'track_url'      => $trackUrl,
        ]);
        return self::send(
            $emailDenunciante,
            $nombre,
            "Actualización de tu denuncia {$referenceCode} — {$estado_label}",
            $html
        );
    }

    /**
     * Alerta de vencimiento inminente al responsable (para cron/recordatorio).
     *
     * @param string $emailResponsable  Correo del responsable
     * @param string $nombreResponsable Nombre del responsable
     * @param string $referenceCode     Código VLD-YYYY-XXXX
     * @param int    $diasRestantes     Días que quedan hasta el deadline
     * @param string $manageUrl         URL al panel de gestión
     */
    public static function sendAlertaVencimientoDenuncia(
        string $emailResponsable,
        string $nombreResponsable,
        string $referenceCode,
        int    $diasRestantes,
        string $manageUrl
    ): bool {
        $html = self::renderTemplate('denuncia_alerta', [
            'nombre_responsable' => $nombreResponsable,
            'reference_code'     => $referenceCode,
            'dias_restantes'     => $diasRestantes,
            'manage_url'         => $manageUrl,
        ]);
        $urgencia = $diasRestantes <= 0 ? '🚨 VENCIDA' : "⚠️ Vence en {$diasRestantes} día" . ($diasRestantes === 1 ? '' : 's');
        return self::send(
            $emailResponsable,
            $nombreResponsable,
            "{$urgencia} — Denuncia {$referenceCode}",
            $html
        );
    }

    /**
     * Alerta al empleador cuando un empleado hace una solicitud nueva.
     *
     * @param string $emailEmpleador  Correo del empleador/responsable
     * @param string $nombreEmpleador Nombre del empleador
     * @param string $nombreEmpleado  Nombre del empleado que solicita
     * @param string $tipo            'permiso' | 'vacaciones'
     * @param string $fechas          Texto con las fechas
     * @param string $dashboardUrl    URL al dashboard del empleador
     */
    public static function sendNuevaSolicitud(
        string $emailEmpleador,
        string $nombreEmpleador,
        string $nombreAdmin,
        string $nombreEmpleado,
        string $tipo,
        string $fechas,
        string $dashboardUrl
    ): bool {
        $html = self::renderTemplate('nueva_solicitud', [
            'nombre_admin'    => $nombreAdmin,
            'nombre_empleado' => $nombreEmpleado,
            'tipo'            => $tipo,
            'fechas'          => $fechas,
            'dashboard_url'   => $dashboardUrl,
        ]);

        $tipoLabels = [
            'permiso'       => 'un permiso',
            'vacaciones'    => 'vacaciones',
            'jornada extra' => 'trabajo fuera de jornada',
        ];
        $tipoLabel = $tipoLabels[$tipo] ?? $tipo;
        $asunto = "{$nombreEmpleado} ha solicitado {$tipoLabel}";

        return self::send($emailEmpleador, $nombreEmpleador, $asunto, $html);
    }
}