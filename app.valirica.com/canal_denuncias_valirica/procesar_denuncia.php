<?php
require 'config.php';
require 'conexion.php'; // archivo donde conectas a la BD

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['honeypot'])) {
    $asunto     = strip_tags($_POST['asunto']);
    $categoria  = strip_tags($_POST['categoria']);
    $descripcion = strip_tags($_POST['descripcion']);
    $anonimo    = isset($_POST['anonimo']);
    $nombre     = !$anonimo ? strip_tags($_POST['nombre']) : '';
    $correo     = !$anonimo ? strip_tags($_POST['correo']) : '';
    $empresa_id = !$anonimo ? intval($_POST['empresa_id']) : 0;

    $correo_empresa = '';
    if (!$anonimo && $empresa_id > 0) {
        $stmt = $conn->prepare("SELECT correo_oficial_comunicacion FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $empresa_id);
        $stmt->execute();
        $stmt->bind_result($correo_empresa);
        $stmt->fetch();
        $stmt->close();
    }

    $mensaje = "🛡️ NUEVA DENUNCIA RECIBIDA\n\n";
    $mensaje .= "Asunto: $asunto\nCategoría: $categoria\n\nDescripción:\n$descripcion\n\n";
    $mensaje .= $anonimo ? "Denuncia anónima.\n" : "Nombre: $nombre\nCorreo: $correo\n";

    $destinos = $destinatarios_confidenciales;
    if (!$anonimo && filter_var($correo_empresa, FILTER_VALIDATE_EMAIL)) {
        $destinos[] = $correo_empresa;
    }

    $headers = "From: Canal de Denuncias <noreply@valirica.com>";
    foreach ($destinos as $to) {
        mail($to, "Nueva denuncia: $asunto", $mensaje, $headers);
    }

    if (!$anonimo && $acuse_habilitado && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        mail($correo, "Acuse de recibo – Valírica", "Gracias por confiar en nosotros. Hemos recibido tu denuncia.", $headers);
    }

    echo "<p style='padding:20px; font-family:sans-serif;'>✅ Gracias por su confianza. Su mensaje ha sido enviado de forma segura.</p>";
} else {
    echo "❌ Error al procesar el formulario.";
}
?>