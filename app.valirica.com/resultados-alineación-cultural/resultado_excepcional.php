<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT empresa, logo, cultura_empresa_tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($usuario = $result->fetch_assoc()) {
    $empresa = htmlspecialchars($usuario['empresa']);
    $logo = htmlspecialchars($usuario['logo']);
    $cultura_empresa_tipo = htmlspecialchars($usuario['cultura_empresa_tipo']);
} else {
    echo "No se encontró información del usuario.";
    exit;
}
$stmt->close();

// Guía cultural
switch ($cultura_empresa_tipo) {
    case 'Jerárquica':
        $nombre_cultura = '<a style="color:#FF7800">Odin</a>, con un enfoque en la estructura y el mantenimiento del orden';
        break;
    case 'Adhocracia':
        $nombre_cultura = '<a style="color:#FF7800">Loki</a>, con el foco en la innovación, la flexibilidad y el cambio';
        break;
    case 'Clan':
        $nombre_cultura = '<a style="color:#FF7800">Freyja</a>, donde la colaboración y el cuidado mutuo son fundamentales.';
        break;
    case 'Mercado':
        $nombre_cultura = '<a style="color:#FF7800">Thor</a>, enfocado en la acción y resolución rápida.';
        break;
    default:
        $nombre_cultura = 'Desconocido';
        break;
}

// Perfiles y promedio
$stmt = $conn->prepare("SELECT id, nombre_persona, alineacion_cultural FROM equipo WHERE usuario_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$perfiles = [];

while ($perfil = $result->fetch_assoc()) {
    $perfiles[] = [
        'nombre' => $perfil['nombre_persona'],
        'id' => $perfil['id'],
        'alineacion' => floatval(str_replace('%', '', $perfil['alineacion_cultural']))
    ];
}
$stmt->close();

$total_alineacion = 0;
$cantidad_miembros = count($perfiles);
foreach ($perfiles as $p) {
    $total_alineacion += $p['alineacion'];
}
$promedio_general = $cantidad_miembros > 0 ? round($total_alineacion / $cantidad_miembros, 1) : 0;

// Redirección (por si se necesita en otros botones)
if ($promedio_general < 30) {
    $url_analisis = "../resultados-alineación-cultural/resultado_bajo.php";
} elseif ($promedio_general <= 55) {
    $url_analisis = "../resultados-alineación-cultural/resultado_medio_bajo.php";
} elseif ($promedio_general <= 75) {
    $url_analisis = "../resultados-alineación-cultural/resultado_medio_alto.php";
} elseif ($promedio_general <= 90) {
    $url_analisis = "../resultados-alineación-cultural/resultado_alto.php";
} else {
    $url_analisis = "../resultados-alineación-cultural/resultado_excepcional.php";
}



// Manejo del formulario POST
$mensaje_confirmacion = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta_nivel = trim($_POST['respuesta'] ?? '');
    if ($respuesta_nivel) {
        // Insertar con el campo específico para nivel de alineación
        $stmt = $conn->prepare("INSERT INTO ProductFeed (usuario_id, respuesta_nivel_alineacion) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $respuesta_nivel);
        $stmt->execute();
        $stmt->close();
        $mensaje_confirmacion = "¡Gracias por tu respuesta!";
    }
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de la Empresa</title>
     <style>
        @import url("https://use.typekit.net/qrv8fyz.css");
                @import url("https://use.typekit.net/qrv8fyz.css");
        body {
            font-family: "Gelica", sans-serif, Arial, Helvetica;
            background-color: #FFF4EE;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            max-width:80%;
        }
        .header-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border-radius: 8px;
            width: fit-content;
        }
        .top-row {
            display: flex;
            align-items: center;
            width: 100%;
        }
        .company-logo {
        width: 200px;
        margin-right: 20px; /* Space between logo and text */
        clip-path: circle(50% at 50% 50%);
        object-fit:cover;
        }
        
        .company-info p {
            margin: 0;
        }
        .company-info h1 {
            font-size: 50px;
            color: #333;
        }
        .company-info p {
            font-size: 25px;
            color: #666;
            text-align: left;
        }
        .buttons-container {
            display: flex;
            width: 100%;
            justify-content: space-around;
            margin-top: 20px;
        }
        .button a {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            text-align:center;
            color: black;
            font-size:25px;
            margin-left:10px;
            margin-right:20px;
        }
        .button img {
            width: 120px;
            margin-bottom: 5px;
        }
        
               /* Estilos para los elementos de la lista */
    .perfil-container ul {
        list-style: none; /* Elimina los bullets predeterminados */
        padding: 0;
    }

    .perfil-container li {
        padding-left: 50px; /* Espacio para el ��cono */
        background: url('/uploads/icon-perfiles.png') no-repeat left center; /* Ajusta esta ruta al ��cono que quieras usar */
        background-size: 35px; /* Tama�0�9o del ��cono */
        margin-bottom: 10px; /* Espacio entre elementos de la lista */
        color: #FF7800; /* Color del texto de la lista */
    }

    .perfil-container li a {
        text-decoration: none; /* Elimina el subrayado de los enlaces */
        color: inherit; /* Hereda el color del elemento li padre */
        
        
    /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/ 
       /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/ 
          /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/
             /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/    /*PARA MOBIL*/ 
    }
        .question {
            margin-top: 30px;
            background: #FFFBF9;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
        }
        textarea {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
            background: #FFF;
            font-family: "Gelica", sans-serif, Arial, Helvetica;
            color: #333;
            box-sizing: border-box;
        }
        textarea:focus {
            border-color: #f28d5c;
            outline: none;
            box-shadow: 0 0 0 2px rgba(242, 141, 92, 0.2);
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #FF7800;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 18px;
            border: none;
            cursor: pointer;
        }
        .confirmation {
            margin-top: 20px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="dashboard-container">

    <!-- Sección Logo + Nombre + Guía -->
    <div class="logo-section">
        <div class="top-row">
           <a href="../dashboard_2.php"> <img src="<?= (substr($logo, 0, 1) === '/') ? $logo : '/' . $logo ?>" alt="Logo de la Empresa" class="company-logo">
            <div class="company-info">
                <h1><?= $empresa ?></h1>
                </a><p style="font-size:30px;"><?= $nombre_cultura ?></p>
            </div>
        </div>
    </div>

    <!-- Separador visual -->
    <img style="padding-top:30px; padding-bottom:30px; width:100%;" src="/uploads/Separador.png">

    <!-- Sección de Botones -->
<div class="botones-section" style="width: 100%;">
    <div style="display: flex; justify-content: space-around; align-items: center; width: 100%;">
        
        <!-- Caja del promedio -->
        <div class="promedio-container" style="background-color: #FFF; border-radius: 15px; padding: 20px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); min-width: 250px; text-align: center;">
            <h2 style="color:#FF7800; font-size:30px; margin:0;">% de Alineación</h2>
            <p style="font-size: 60px; color:#004758; font-weight: bold; margin:10px 0;">
                <?= $promedio_general ?>%
            </p>
            <a href="<?= $url_analisis ?>" style="color:#666; font-size:25px; text-decoration:underline; background:none; border:none; padding:0; cursor:pointer;">
    Ver Análisis</br>de Resultado
</a>
        </div>

        <!-- Botones existentes -->
        <div class="buttons-container" style="flex: 1; display: flex; justify-content: space-around;">
            <div class="button">
                <a href="ver_analisis.php?user_id=<?= $user_id ?>">
                    <img src="/uploads/Boton-proposito.png" alt="Icon 1">
                    <span style="color:#FF7800; font-size:25px;"><strong>Propósito</br>& Valores</strong></span>
                </a>
            </div>
            <div class="button">
                <a href="dimension_cultura_hofstede.php?user_id=<?= $user_id ?>">
                    <img src="/uploads/Boton-cultura.png" alt="Icon 2">
                    <span style="color:#FF7800; font-size:25px;"><strong>Alineación</br>Cultural</strong></span>
                </a>
            </div>
            <div class="button">
                <a href="rituales-de-marca.php?user_id=<?= $user_id ?>">
                    <img src="/uploads/Boton-rituales.png" alt="Icon 3">
                    <span style="color:#FF7800; font-size:25px;"><strong>Rituales</br>de Marca</strong></span>
                </a>
            </div>
        </div>
    </div>
</div>

    <!-- Separador visual -->
    <img style="padding-top:30px; padding-bottom:30px; width:100%;" src="/uploads/Separador.png">


 <!-- Contenido específico para excepcional -->
<div style="display: flex; width: 100%; box-sizing: border-box;">

    <!-- Columna 1: Imagen + Texto + Imagen -->
  <div style="width: 30%; padding:5px; text-align: center;">
    <!-- Imagen principal -->
    <img src="../uploads/Niveles-5.png" alt="Imagen relacionada" style="max-width:100%; height: auto; margin-bottom: 5px;">


  </div>
  
  <!-- Columna 2: Texto del análisis -->
<div style="width: 70%; padding-left: 30px;">
  <h1 class="company-info" style="text-align: left; color: #333;">
    <?= $promedio_general ?>% Calificación EXCEPCIONAL </br><a style="color:#FF7800;"> Nivel: VALHALLA - Destiny</a>
  </h1>

  <h1 class="company-info" style="text-align: left; color:#FF7800;">
   
  </h1>

  <!-- Contenedor flex para el texto y la imagen en la misma línea -->
  <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
     <img src="../uploads/Niveles-5.png" alt="Siguiente nivel" style="max-width: 100px; height: auto;">
     <p style="font-size: 20px; font-weight: bold; color: #FF7800; margin: 0;">LO </br> LOGRASTE: </br> <a style="color:#002135">Ya eres invencible</a></p>
   
  </div>
</div>


</div>

<p style="font-size: 25px; color: #666; text-align: left;" class="descripcion">
¡WOW!, tu cultura es un caso de estudio. Has alcanzado el máximo nivel, una cultura que es símbolo, ejemplo y legado. Ya no construyes reputación: la encarnas.   </p>
    <div style="display: flex; align-items: left; gap: 10px; margin-top: 10px;">
     <img src="../uploads/icon-perfiles.png" alt="Siguiente nivel" style="width: 80px; height:50px;margin-right:15px; ">
     <p style="font-size: 30px; font-weight: bold; color: #002135; margin: 0;">Haz llegado a Valhalla. Lo que proyectas es lo que eres.</p>
   
  </div>
       

        
    </div>


<div class="question">
    <h1>Estamos construyendo una sección especial para ti, peo también nos encantaría escucharte y saber... ¿Qué te gustaría encontrar aquí?</h1>
    <form method="POST">
        <textarea name="respuesta" id="respuesta" rows="4" placeholder="Escribe tu respuesta aquí..." required></textarea>
        <button type="submit" class="btn">Enviar</button>
    </form>

    <?php if ($mensaje_confirmacion): ?>
        <div class="confirmation"><?= $mensaje_confirmacion ?></div>
    <?php endif; ?>
</div>



</div>
</body>
</html>