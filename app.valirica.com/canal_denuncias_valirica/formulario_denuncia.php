<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Canal de Denuncias - Valírica</title>
  <style>
    @import url("https://use.typekit.net/qrv8fyz.css");

    * {
      box-sizing: border-box;
    }

    body {
      font-family: "Gelica", sans-serif, Arial, Helvetica;
      background-color: #FFF4EE;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .login-container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
      width: 100%;
      max-width: 800px;
    }

    .login-logo {
      width: 500px;
      margin-bottom: 40px;
    }

    h1 {
      color: #FF7800;
      font-size: 36px;
      margin-bottom: 30px;
      text-align: center;
    }

    .intro {
      font-size: 22px;
      text-align: center;
      margin-bottom: 30px;
      color: #333;
      padding: 0 10px;
    }

    form {
      width: 100%;
    }

    label {
      display: block;
      font-size: 25px;
      color: #004758;
      margin-bottom: 5px;
      text-align: left;
    }

    .input-field,
    select,
    textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 20px;
      outline: none;
      transition: border-color 0.3s ease;
      font-family: "Gelica", sans-serif, Arial, Helvetica;
    }

    .input-field:focus,
    select:focus,
    textarea:focus {
      border-color: #FF7800;
    }

    .btn {
      background-color: #FF7800;
      color: white;
      border: none;
      padding: 14px;
      width: 100%;
      font-size: 30px;
      border-radius: 30px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-family: "Gelica", sans-serif, Arial, Helvetica;
    }

    .btn:hover {
      background-color: #e96d00;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      font-size: 20px;
      margin-bottom: 20px;
    }

    .checkbox-label input[type="checkbox"] {
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <img src="/uploads/logo-valirica.png" alt="Valírica" class="login-logo">
    <h1>Canal de Denuncias Internas</h1>
    <p class="intro">
      Este canal permite reportar situaciones de forma <strong>confidencial y segura</strong>.<br>
      Puedes usarlo con o sin identificarte.
    </p>

    <form action="procesar_denuncia.php" method="POST" enctype="multipart/form-data">
      <label>Asunto*</label>
      <input class="input-field" type="text" name="asunto" required>

      <label>Categoría*</label>
      <select class="input-field" name="categoria" required>
        <option value="">Seleccione</option>
        <option value="Acoso laboral">Acoso laboral</option>
        <option value="Fraude">Fraude</option>
        <option value="Discriminación">Discriminación</option>
        <option value="Violación del código ético">Violación del código ético</option>
        <option value="Otro">Otro</option>
      </select>

      <label>Descripción*</label>
      <textarea class="input-field" name="descripcion" rows="5" required></textarea>

      <label>Archivo adjunto (opcional, máx. 10MB)</label>
      <input class="input-field" type="file" name="archivo">

      <label class="checkbox-label">
        <input type="checkbox" name="anonimo" value="1" onchange="toggleIdentidad(this)">
        Quiero permanecer en el anonimato
      </label>

      <div id="camposIdentidad">
        <label>Nombre completo</label>
        <input class="input-field" type="text" name="nombre">

        <label>Correo electrónico</label>
        <input class="input-field" type="email" name="correo">

        <label>ID de empresa (interno)</label>
        <input class="input-field" type="text" name="empresa_id">
      </div>

      <!-- Honeypot -->
      <input type="text" name="honeypot" style="display:none;">

      <button class="btn" type="submit">Enviar denuncia</button>
    </form>
  </div>

  <script>
    function toggleIdentidad(checkbox) {
      document.getElementById("camposIdentidad").style.display = checkbox.checked ? "none" : "block";
    }

    // Aplicar estado inicial por si el checkbox ya viene marcado
    window.onload = function () {
      const checkbox = document.querySelector('input[name="anonimo"]');
      if (checkbox.checked) toggleIdentidad(checkbox);
    };
  </script>
</body>
</html>
