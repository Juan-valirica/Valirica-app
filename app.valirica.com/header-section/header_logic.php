<?php

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];





// Obtener datos del usuario
$stmt = $conn->prepare("SELECT empresa, logo, cultura_empresa_tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($usuario = $result->fetch_assoc()) {
    $empresa = htmlspecialchars($usuario['empresa']);
    $logo = htmlspecialchars($usuario['logo']);
$cultura_empresa_tipo = htmlspecialchars($usuario['cultura_empresa_tipo'] ?? '');
} else {
    echo "No se encontró información del usuario.";
    exit;
}
$stmt->close();

// Determinar nombre de cultura
switch ($cultura_empresa_tipo) {
    case 'Jerárquica':
        $nombre_cultura = ' <a style="color:#FF7800">Odin</a> es tu guía cultural, con un enfoque en la estructura y el mantenimiento del orden';
        break;
    case 'Adhocracia':
        $nombre_cultura = '<a style="color:#FF7800">Loki</a> es tu guía cultural, con el foco en la innovación, la flexibilidad y el cambio';
        break;
    case 'Clan':
        $nombre_cultura = '<a style="color:#FF7800">Freyja</a> es tu guía cultural, donde la colaboración y el cuidado mutuo son fundamentales.';
        break;
    case 'Mercado':
        $nombre_cultura = '<a style="color:#FF7800">Thor</a> es tu guía cultural, enfocado en la acción y resolución rápida.';
        break;
    default:
        $nombre_cultura = 'Desconocido';
        break;
}

// Obtener cultura ideal
$stmt = $conn->prepare("
    SELECT 
        distancia_poder, individualismo, masculinidad, incertidumbre, largo_plazo, indulgencia 
    FROM cultura_ideal 
    WHERE usuario_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cultura_ideal = $result->fetch_assoc();
$stmt->close();

// Normaliza valores ideales
$valores_ideales = [];
foreach ($cultura_ideal as $clave => $valor) {
    $valores_ideales[$clave] = round($valor / 5, 3); // escala -1 a 1
}

// Obtener perfiles del equipo
$stmt = $conn->prepare("
    SELECT 
        hofstede_poder as distancia_poder,
        hofstede_individualismo as individualismo,
        hofstede_resultados as masculinidad,
        hofstede_incertidumbre as incertidumbre,
        hofstede_largo_plazo as largo_plazo,
        hofstede_espontaneidad as indulgencia
    FROM equipo 
    WHERE usuario_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_alineacion = 0;
$cantidad_miembros = 0;

while ($row = $result->fetch_assoc()) {
    $suma = 0;
    $dimensiones = 0;

    foreach ($valores_ideales as $clave => $ideal) {
        if (isset($row[$clave])) {
            $real = floatval($row[$clave]);
            $alineacion = 1 - abs($real - $ideal);
            $suma += $alineacion;
            $dimensiones++;
        }
    }

    if ($dimensiones > 0) {
        $alineacion_individual = $suma / $dimensiones;
        $total_alineacion += $alineacion_individual;
        $cantidad_miembros++;
    }
}
$stmt->close();

$promedio_general = ($cantidad_miembros > 0)
    ? round(($total_alineacion / $cantidad_miembros) * 100, 1)
    : 0;

// Determinar URL de análisis
if ($promedio_general < 30) {
    $url_analisis = "resultados-alineación-cultural/resultado_bajo.php";
} elseif ($promedio_general <= 55) {
    $url_analisis = "resultados-alineación-cultural/resultado_medio_bajo.php";
} elseif ($promedio_general <= 75) {
    $url_analisis = "resultados-alineación-cultural/resultado_medio_alto.php";
} elseif ($promedio_general <= 90) {
    $url_analisis = "resultados-alineación-cultural/resultado_alto.php";
} else {
    $url_analisis = "resultados-alineación-cultural/resultado_excepcional.php";
}


?>


