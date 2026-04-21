/**
 * ====================================
 * JAVASCRIPT PARA GESTIÓN DE HORARIOS
 * ====================================
 * Incluir este script en a-desempeno-dashboard.php
 * antes del cierre del </body>
 * ====================================
 */

// ============================================
// VARIABLES GLOBALES
// ============================================

let wizardCurrentStep = 1;
let wizardData = {
  nombre: '',
  descripcion: '',
  codigo_corto: '',
  tipo_jornada: 'fija',
  color_hex: '#184656',
  horas_semanales_esperadas: 40.00,
  tolerancia_entrada_min: 15,
  tolerancia_salida_min: 5,
  turnos: []
};


// ============================================
// WIZARD: CREAR/EDITAR JORNADA
// ============================================

function abrirModalCrearJornada() {
  // Reset wizard
  wizardCurrentStep = 1;
  wizardData = {
    nombre: '',
    descripcion: '',
    codigo_corto: '',
    tipo_jornada: 'fija',
    color_hex: '#184656',
    horas_semanales_esperadas: 40.00,
    tolerancia_entrada_min: 15,
    tolerancia_salida_min: 5,
    turnos: []
  };

  document.getElementById('wizard-title').textContent = 'Crear Nueva Jornada';
  document.getElementById('modalJornadaWizard').style.display = 'flex';

  renderWizardStep();
}

function cerrarModalJornada() {
  document.getElementById('modalJornadaWizard').style.display = 'none';
}

function wizardNextStep() {
  // Validar paso actual antes de avanzar
  if (!validarPasoActual()) return;

  // Guardar datos del paso actual
  guardarDatosPaso();

  // Avanzar al siguiente paso
  if (wizardCurrentStep < 4) {
    wizardCurrentStep++;
    renderWizardStep();
  } else {
    // Último paso: enviar datos
    submitCrearJornada();
  }
}

function wizardPrevStep() {
  if (wizardCurrentStep > 1) {
    wizardCurrentStep--;
    renderWizardStep();
  }
}

function renderWizardStep() {
  const content = document.getElementById('wizard-content');
  const indicator = document.getElementById('wizard-step-indicator');
  const progressBar = document.getElementById('wizard-progress-bar');
  const btnNext = document.getElementById('wizard-btn-next');
  const btnBack = document.getElementById('wizard-btn-back');

  // Actualizar indicadores
  indicator.textContent = `Paso ${wizardCurrentStep} de 4`;
  progressBar.style.width = `${wizardCurrentStep * 25}%`;

  // Mostrar/ocultar botón atrás
  btnBack.style.display = wizardCurrentStep > 1 ? 'block' : 'none';

  // Cambiar texto del botón siguiente
  btnNext.textContent = wizardCurrentStep === 4 ? '✓ Guardar Jornada' : 'Siguiente →';

  // Renderizar contenido del paso
  switch (wizardCurrentStep) {
    case 1:
      content.innerHTML = renderPaso1();
      break;
    case 2:
      content.innerHTML = renderPaso2();
      initPaso2Handlers();
      break;
    case 3:
      content.innerHTML = renderPaso3();
      break;
    case 4:
      content.innerHTML = renderPaso4();
      break;
  }
}


// ============================================
// PASO 1: INFORMACIÓN BÁSICA
// ============================================

function renderPaso1() {
  return `
    <div>
      <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 var(--space-5); color: var(--c-secondary);">
        Información Básica
      </h3>

      <div style="margin-bottom: var(--space-4);">
        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
          Nombre de la jornada <span style="color: #FF3B6D;">*</span>
        </label>
        <input
          type="text"
          id="wizard-nombre"
          placeholder="Ej: Oficina - Diurno"
          value="${wizardData.nombre}"
          style="
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--perf-border);
            border-radius: 8px;
            font-size: 14px;
          "
        >
      </div>

      <div style="margin-bottom: var(--space-4);">
        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
          Descripción (opcional)
        </label>
        <textarea
          id="wizard-descripcion"
          placeholder="Horario estándar para personal administrativo..."
          style="
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--perf-border);
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
          "
        >${wizardData.descripcion}</textarea>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); margin-bottom: var(--space-4);">
        <div>
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
            Código corto
          </label>
          <input
            type="text"
            id="wizard-codigo"
            placeholder="Ej: OFF-D"
            value="${wizardData.codigo_corto}"
            maxlength="20"
            style="
              width: 100%;
              padding: 12px 16px;
              border: 1px solid var(--perf-border);
              border-radius: 8px;
              font-size: 14px;
            "
          >
        </div>

        <div>
          <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
            Color de identificación
          </label>
          <input
            type="color"
            id="wizard-color"
            value="${wizardData.color_hex}"
            style="
              width: 100%;
              height: 46px;
              padding: 4px;
              border: 1px solid var(--perf-border);
              border-radius: 8px;
              cursor: pointer;
            "
          >
        </div>
      </div>

      <div style="margin-bottom: var(--space-4);">
        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
          Tipo de jornada <span style="color: #FF3B6D;">*</span>
        </label>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-3);">
          <label class="tipo-jornada-option" data-tipo="fija" style="
            padding: 16px;
            border: 2px solid ${wizardData.tipo_jornada === 'fija' ? 'var(--c-accent)' : 'var(--perf-border)'};
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            background: ${wizardData.tipo_jornada === 'fija' ? 'var(--c-accent)10' : 'white'};
          ">
            <input type="radio" name="tipo_jornada" value="fija" ${wizardData.tipo_jornada === 'fija' ? 'checked' : ''} style="display: none;">
            <div style="font-size: 24px; margin-bottom: var(--space-1);">📅</div>
            <div style="font-size: 13px; font-weight: 600;">Fija</div>
            <div style="font-size: 11px; opacity: 0.7;">Horarios constantes</div>
          </label>

          <label class="tipo-jornada-option" data-tipo="rotativa" style="
            padding: 16px;
            border: 2px solid ${wizardData.tipo_jornada === 'rotativa' ? 'var(--c-accent)' : 'var(--perf-border)'};
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            background: ${wizardData.tipo_jornada === 'rotativa' ? 'var(--c-accent)10' : 'white'};
          ">
            <input type="radio" name="tipo_jornada" value="rotativa" ${wizardData.tipo_jornada === 'rotativa' ? 'checked' : ''} style="display: none;">
            <div style="font-size: 24px; margin-bottom: var(--space-1);">🔄</div>
            <div style="font-size: 13px; font-weight: 600;">Rotativa</div>
            <div style="font-size: 11px; opacity: 0.7;">Turnos rotativos</div>
          </label>

          <label class="tipo-jornada-option" data-tipo="flexible" style="
            padding: 16px;
            border: 2px solid ${wizardData.tipo_jornada === 'flexible' ? 'var(--c-accent)' : 'var(--perf-border)'};
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            background: ${wizardData.tipo_jornada === 'flexible' ? 'var(--c-accent)10' : 'white'};
          ">
            <input type="radio" name="tipo_jornada" value="flexible" ${wizardData.tipo_jornada === 'flexible' ? 'checked' : ''} style="display: none;">
            <div style="font-size: 24px; margin-bottom: var(--space-1);">⚡</div>
            <div style="font-size: 13px; font-weight: 600;">Flexible</div>
            <div style="font-size: 11px; opacity: 0.7;">Core hours</div>
          </label>
        </div>
      </div>

      <script>
        // Event listeners para radio buttons visuales
        document.querySelectorAll('.tipo-jornada-option').forEach(option => {
          option.addEventListener('click', function() {
            const tipo = this.dataset.tipo;
            document.querySelectorAll('.tipo-jornada-option').forEach(opt => {
              opt.style.borderColor = 'var(--perf-border)';
              opt.style.background = 'white';
            });
            this.style.borderColor = 'var(--c-accent)';
            this.style.background = 'var(--c-accent)10';
            this.querySelector('input').checked = true;
          });
        });
      </script>
    </div>
  `;
}


// ============================================
// PASO 2: DEFINIR TURNOS
// ============================================

function renderPaso2() {
  let turnosHTML = '';

  if (wizardData.turnos.length === 0) {
    wizardData.turnos.push({
      nombre_turno: 'Lunes a Viernes',
      dias: [1, 2, 3, 4, 5],
      hora_inicio: '09:00',
      hora_fin: '18:00',
      cruza_medianoche: 0,
      modalidad: 'presencial',
      requiere_geo: 0,
      geo_nombre_lugar: '',
      geo_lat: '',
      geo_lng: '',
      geo_radio_metros: 100,
      geo_modo_estricto: 0
    });
  }

  wizardData.turnos.forEach((turno, index) => {
    turnosHTML += renderTurnoCard(turno, index);
  });

  return `
    <div>
      <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 var(--space-3); color: var(--c-secondary);">
        Definir Turnos de Trabajo
      </h3>
      <p style="color: var(--c-body); opacity: 0.7; margin: 0 0 var(--space-5); font-size: 14px;">
        Configura los horarios para cada día de la semana
      </p>

      <div id="turnos-container">
        ${turnosHTML}
      </div>

      <button
        type="button"
        onclick="agregarTurno()"
        style="
          width: 100%;
          padding: 12px;
          background: white;
          border: 2px dashed var(--perf-border);
          border-radius: 8px;
          color: var(--c-body);
          font-size: 14px;
          font-weight: 600;
          cursor: pointer;
          transition: all 0.2s ease;
          margin-top: var(--space-4);
        "
        onmouseover="this.style.borderColor='var(--c-accent)'; this.style.color='var(--c-accent)'"
        onmouseout="this.style.borderColor='var(--perf-border)'; this.style.color='var(--c-body)'"
      >
        ➕ Agregar Otro Turno
      </button>
    </div>
  `;
}

function renderTurnoCard(turno, index) {
  const diasSemana = [
    { num: 1, label: 'Lun' },
    { num: 2, label: 'Mar' },
    { num: 3, label: 'Mié' },
    { num: 4, label: 'Jue' },
    { num: 5, label: 'Vie' },
    { num: 6, label: 'Sáb' },
    { num: 7, label: 'Dom' }
  ];

  // ── Días checkboxes ──────────────────────────────────────────────────────────
  let diasHTML = '';
  diasSemana.forEach(dia => {
    const checked = turno.dias.includes(dia.num);
    diasHTML += `
      <label class="dia-checkbox" style="
        flex: 1; min-width: 50px; padding: 10px 8px;
        border: 2px solid ${checked ? 'var(--c-accent)' : 'var(--perf-border)'};
        border-radius: 8px; text-align: center; cursor: pointer;
        font-size: 12px; font-weight: 600;
        background: ${checked ? 'rgba(239,127,27,0.08)' : 'white'};
        transition: all 0.2s ease; user-select: none;
      ">
        <input type="checkbox" value="${dia.num}" ${checked ? 'checked' : ''}
          onchange="toggleDiaTurno(${index}, ${dia.num})" style="display:none;">
        ${dia.label}
      </label>`;
  });

  // ── Modalidad buttons ────────────────────────────────────────────────────────
  const modalidadActual = turno.modalidad || 'presencial';
  const mLabels = { presencial: '🏢 Presencial', remoto: '🏠 Teletrabajo', hibrido: '🔄 Híbrido' };
  let modalidadBtns = '';
  ['presencial', 'remoto', 'hibrido'].forEach(m => {
    const active = modalidadActual === m;
    modalidadBtns += `<button type="button" onclick="actualizarModalidadTurno(${index}, '${m}')" style="
        flex:1; padding:8px 4px;
        border: 2px solid ${active ? 'var(--c-accent)' : 'var(--perf-border)'};
        border-radius:8px;
        background:${active ? 'rgba(239,127,27,0.08)' : 'white'};
        font-size:12px; font-weight:600; cursor:pointer;
        color:${active ? 'var(--c-accent)' : 'var(--c-body)'};
        transition:all 0.2s ease;
      ">${mLabels[m]}</button>`;
  });

  // ── Radio metros buttons ─────────────────────────────────────────────────────
  const radioActual = turno.geo_radio_metros || 100;
  let radioBtns = '';
  [50, 100, 200, 500].forEach(r => {
    const active = radioActual === r;
    radioBtns += `<button type="button"
        onclick="actualizarTurno(${index}, 'geo_radio_metros', ${r}); renderWizardStep();" style="
        padding:6px 14px;
        border:2px solid ${active ? 'var(--c-accent)' : 'var(--perf-border)'};
        border-radius:6px;
        background:${active ? 'rgba(239,127,27,0.08)' : 'white'};
        font-size:12px; font-weight:600; cursor:pointer;
        color:${active ? 'var(--c-accent)' : 'var(--c-body)'};
      ">${r}m</button>`;
  });

  const geoSecVisible  = modalidadActual !== 'remoto';
  const geoFldVisible  = !!turno.requiere_geo;
  const modoEstrictoTxt = turno.geo_modo_estricto
    ? 'El empleado <strong>no puede fichar</strong> fuera del perímetro.'
    : 'El empleado puede fichar fuera del perímetro, pero quedará marcado para revisión del administrador.';
  const geoNombreVal = (turno.geo_nombre_lugar || '').replace(/"/g, '&quot;');

  return `
    <div class="turno-card" style="
      background: var(--perf-bg); border: 1px solid var(--perf-border);
      border-radius: 12px; padding: var(--space-4); margin-bottom: var(--space-4);
    ">
      <!-- Cabecera del turno -->
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-3);">
        <h4 style="font-size:15px; font-weight:700; margin:0;">Turno ${index + 1}</h4>
        ${wizardData.turnos.length > 1 ? `
          <button type="button" onclick="eliminarTurno(${index})"
            style="background:none; border:none; color:#FF3B6D; cursor:pointer; font-size:18px; padding:4px 8px;"
            title="Eliminar turno">🗑️</button>` : ''}
      </div>

      <!-- Nombre del turno -->
      <div style="margin-bottom:var(--space-3);">
        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:var(--space-2);">Nombre del turno:</label>
        <input type="text" value="${turno.nombre_turno}"
          onchange="actualizarTurno(${index}, 'nombre_turno', this.value)"
          placeholder="Ej: Lunes a Viernes"
          style="width:100%; padding:10px 14px; border:1px solid var(--perf-border); border-radius:6px; font-size:14px;">
      </div>

      <!-- Días -->
      <div style="margin-bottom:var(--space-3);">
        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:var(--space-2);">Días de la semana:</label>
        <div style="display:flex; gap:var(--space-2); flex-wrap:wrap;">${diasHTML}</div>
      </div>

      <!-- Hora inicio / fin -->
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-3); margin-bottom:var(--space-3);">
        <div>
          <label style="display:block; font-size:13px; font-weight:600; margin-bottom:var(--space-2);">Hora inicio:</label>
          <input type="time" value="${turno.hora_inicio}"
            onchange="actualizarTurno(${index}, 'hora_inicio', this.value); calcularHoras(${index})"
            style="width:100%; padding:10px 14px; border:1px solid var(--perf-border); border-radius:6px; font-size:14px;">
        </div>
        <div>
          <label style="display:block; font-size:13px; font-weight:600; margin-bottom:var(--space-2);">Hora fin:</label>
          <input type="time" value="${turno.hora_fin}"
            onchange="actualizarTurno(${index}, 'hora_fin', this.value); calcularHoras(${index})"
            style="width:100%; padding:10px 14px; border:1px solid var(--perf-border); border-radius:6px; font-size:14px;">
        </div>
      </div>

      <!-- Cruza medianoche -->
      <div style="margin-bottom:var(--space-3);">
        <label style="display:flex; align-items:center; gap:var(--space-2); cursor:pointer;">
          <input type="checkbox" ${turno.cruza_medianoche ? 'checked' : ''}
            onchange="actualizarTurno(${index}, 'cruza_medianoche', this.checked ? 1 : 0); calcularHoras(${index})"
            style="width:18px; height:18px; cursor:pointer;">
          <span style="font-size:13px;">🌙 Este turno cruza medianoche (ej: 10 PM – 6 AM)</span>
        </label>
      </div>

      <!-- Separador -->
      <div style="height:1px; background:var(--perf-border); margin:var(--space-3) 0;"></div>

      <!-- Modalidad -->
      <div style="margin-bottom:var(--space-3);">
        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:var(--space-2);">Modalidad:</label>
        <div style="display:flex; gap:var(--space-2);">${modalidadBtns}</div>
      </div>

      <!-- Sección geo (oculta si Teletrabajo) -->
      <div id="geo-section-${index}" style="display:${geoSecVisible ? 'block' : 'none'};">

        <!-- Toggle geo -->
        <label style="
          display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3);
          border:1px solid ${geoFldVisible ? 'var(--c-accent)' : 'var(--perf-border)'};
          background:${geoFldVisible ? 'rgba(239,127,27,0.04)' : 'white'};
          border-radius:8px; cursor:pointer; margin-bottom:var(--space-2); transition:all 0.2s ease;
        ">
          <input type="checkbox" ${turno.requiere_geo ? 'checked' : ''}
            onchange="toggleGeoTurno(${index}, this.checked)"
            style="width:18px; height:18px; cursor:pointer; accent-color:var(--c-accent); flex-shrink:0;">
          <div>
            <div style="font-size:13px; font-weight:600;">📍 Verificar ubicación al fichar</div>
            <div style="font-size:11px; opacity:0.6; margin-top:2px;">El empleado debe estar dentro del perímetro al registrar entrada o salida</div>
          </div>
        </label>

        <!-- Campos geo (visibles si requiere_geo=1) -->
        <div id="geo-fields-${index}" style="
          display:${geoFldVisible ? 'block' : 'none'};
          padding:var(--space-4); background:#F8FAFC;
          border:1px solid var(--perf-border); border-radius:8px; margin-bottom:var(--space-2);
        ">
          <!-- Nombre del lugar -->
          <div style="margin-bottom:var(--space-3);">
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">
              Nombre del lugar <span style="color:#FF3B6D;">*</span>
            </label>
            <input type="text" value="${geoNombreVal}"
              onchange="actualizarTurno(${index}, 'geo_nombre_lugar', this.value)"
              placeholder="Ej: Oficina Madrid — Gran Vía"
              style="width:100%; padding:8px 12px; border:1px solid var(--perf-border); border-radius:6px; font-size:13px;">
          </div>

          <!-- Coordenadas -->
          <div style="margin-bottom:var(--space-3);">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
              <label style="font-size:12px; font-weight:600;">Coordenadas del centro de trabajo</label>
              <button type="button" onclick="usarUbicacionActual(${index}, event)" style="
                padding:4px 12px; background:var(--c-secondary); border:none;
                border-radius:6px; color:white; font-size:11px; font-weight:600; cursor:pointer;">
                📍 Usar mi ubicación
              </button>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-2);">
              <input type="number" id="geo-lat-${index}" value="${turno.geo_lat || ''}"
                onchange="actualizarTurno(${index}, 'geo_lat', this.value ? parseFloat(this.value) : '')"
                placeholder="Latitud (ej: 40.4168)" step="any"
                style="padding:8px 12px; border:1px solid var(--perf-border); border-radius:6px; font-size:12px;">
              <input type="number" id="geo-lng-${index}" value="${turno.geo_lng || ''}"
                onchange="actualizarTurno(${index}, 'geo_lng', this.value ? parseFloat(this.value) : '')"
                placeholder="Longitud (ej: -3.7038)" step="any"
                style="padding:8px 12px; border:1px solid var(--perf-border); border-radius:6px; font-size:12px;">
            </div>
            <p style="font-size:11px; opacity:0.5; margin:4px 0 0;">
              💡 Si estás en el lugar de trabajo, pulsa "Usar mi ubicación" para rellenar automáticamente.
            </p>
          </div>

          <!-- Radio del perímetro -->
          <div style="margin-bottom:var(--space-3);">
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:var(--space-2);">
              Radio del perímetro aceptado
            </label>
            <div style="display:flex; gap:var(--space-1); flex-wrap:wrap;">${radioBtns}</div>
            <p style="font-size:11px; opacity:0.5; margin:var(--space-1) 0 0;">
              Se aceptará el fichaje si el empleado está a menos de <strong>${radioActual}m</strong> del centro definido.
            </p>
          </div>

          <!-- Modo estricto -->
          <label style="
            display:flex; align-items:flex-start; gap:var(--space-2); cursor:pointer; padding:var(--space-3);
            background:${turno.geo_modo_estricto ? '#FFF0F3' : 'white'};
            border:1px solid ${turno.geo_modo_estricto ? 'rgba(255,59,109,0.3)' : 'var(--perf-border)'};
            border-radius:6px; transition:all 0.2s ease;
          ">
            <input type="checkbox" ${turno.geo_modo_estricto ? 'checked' : ''}
              onchange="actualizarTurno(${index}, 'geo_modo_estricto', this.checked ? 1 : 0); renderWizardStep();"
              style="width:16px; height:16px; margin-top:2px; cursor:pointer; flex-shrink:0;">
            <div>
              <div style="font-size:12px; font-weight:600; color:${turno.geo_modo_estricto ? '#FF3B6D' : 'var(--c-body)'};">🔒 Modo estricto</div>
              <div style="font-size:11px; opacity:0.75; margin-top:2px; line-height:1.4;">${modoEstrictoTxt}</div>
            </div>
          </label>
        </div>
      </div>

      <!-- Horas calculadas -->
      <div id="horas-dia-${index}" style="
        padding:var(--space-2); background:white;
        border-radius:6px; font-size:12px; opacity:0.8; margin-top:var(--space-2);
      "><!-- Se llenará con JS --></div>
    </div>
  `;
}

function initPaso2Handlers() {
  // Calcular horas para cada turno
  wizardData.turnos.forEach((turno, index) => {
    calcularHoras(index);
  });
}

function toggleDiaTurno(turnoIndex, dia) {
  const turno = wizardData.turnos[turnoIndex];
  const index = turno.dias.indexOf(dia);

  if (index > -1) {
    turno.dias.splice(index, 1);
  } else {
    turno.dias.push(dia);
    turno.dias.sort((a, b) => a - b);
  }

  // Re-renderizar paso 2
  renderWizardStep();
}

function actualizarTurno(turnoIndex, campo, valor) {
  wizardData.turnos[turnoIndex][campo] = valor;
}

function agregarTurno() {
  wizardData.turnos.push({
    nombre_turno: `Turno ${wizardData.turnos.length + 1}`,
    dias: [],
    hora_inicio: '09:00',
    hora_fin: '18:00',
    cruza_medianoche: 0,
    modalidad: 'presencial',
    requiere_geo: 0,
    geo_nombre_lugar: '',
    geo_lat: '',
    geo_lng: '',
    geo_radio_metros: 100,
    geo_modo_estricto: 0
  });
  renderWizardStep();
}

function eliminarTurno(index) {
  if (wizardData.turnos.length > 1) {
    wizardData.turnos.splice(index, 1);
    renderWizardStep();
  }
}

function calcularHoras(turnoIndex) {
  const turno = wizardData.turnos[turnoIndex];
  const [h1, m1] = turno.hora_inicio.split(':').map(Number);
  const [h2, m2] = turno.hora_fin.split(':').map(Number);

  let minutos = 0;

  if (turno.cruza_medianoche) {
    // Calcula desde h1:m1 hasta medianoche + desde medianoche hasta h2:m2
    minutos = (24 * 60 - (h1 * 60 + m1)) + (h2 * 60 + m2);
  } else {
    minutos = (h2 * 60 + m2) - (h1 * 60 + m1);
  }

  const horas = (minutos / 60).toFixed(1);
  const diasCount = turno.dias.length;
  const horasSemanal = (horas * diasCount).toFixed(1);

  const elem = document.getElementById(`horas-dia-${turnoIndex}`);
  if (elem) {
    elem.innerHTML = `
      ⏱️ <strong>${horas} hrs</strong> por día ×
      <strong>${diasCount}</strong> día(s) =
      <strong>${horasSemanal} hrs/semana</strong>
    `;
  }
}


// ============================================
// PASO 3: TOLERANCIAS Y POLÍTICAS
// ============================================

function renderPaso3() {
  return `
    <div>
      <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 var(--space-3); color: var(--c-secondary);">
        Políticas de Asistencia
      </h3>
      <p style="color: var(--c-body); opacity: 0.7; margin: 0 0 var(--space-5); font-size: 14px;">
        Define las tolerancias para entrada y salida
      </p>

      <div style="margin-bottom: var(--space-5);">
        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
          Tolerancia de entrada (minutos)
        </label>
        <input
          type="number"
          id="wizard-tolerancia-entrada"
          value="${wizardData.tolerancia_entrada_min}"
          min="0"
          max="60"
          style="
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--perf-border);
            border-radius: 8px;
            font-size: 14px;
          "
        >
        <p style="font-size: 12px; opacity: 0.6; margin: var(--space-2) 0 0;">
          Empleados pueden llegar hasta ${wizardData.tolerancia_entrada_min} minutos tarde sin penalización
        </p>
      </div>

      <div style="margin-bottom: var(--space-5);">
        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
          Tolerancia de salida (minutos)
        </label>
        <input
          type="number"
          id="wizard-tolerancia-salida"
          value="${wizardData.tolerancia_salida_min}"
          min="0"
          max="60"
          style="
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--perf-border);
            border-radius: 8px;
            font-size: 14px;
          "
        >
        <p style="font-size: 12px; opacity: 0.6; margin: var(--space-2) 0 0;">
          Empleados pueden salir hasta ${wizardData.tolerancia_salida_min} minutos después sin penalización
        </p>
      </div>

      <div style="margin-bottom: var(--space-5);">
        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: var(--space-2);">
          Horas semanales esperadas
        </label>
        <input
          type="number"
          id="wizard-horas-semanales"
          value="${wizardData.horas_semanales_esperadas}"
          min="1"
          max="168"
          step="0.5"
          style="
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--perf-border);
            border-radius: 8px;
            font-size: 14px;
          "
        >
        <div id="horas-calculadas" style="
          margin-top: var(--space-2);
          padding: var(--space-3);
          background: var(--perf-bg);
          border-radius: 8px;
          font-size: 13px;
        ">
          <!-- Se llenará con JS -->
        </div>
      </div>

      <div style="
        padding: var(--space-4);
        background: #00D98F10;
        border: 1px solid #00D98F30;
        border-radius: 8px;
      ">
        <div style="font-size: 13px; line-height: 1.6;">
          💡 <strong>Consejo:</strong> Las tolerancias ayudan a evitar marcar como "tarde"
          a empleados que llegaron casi a tiempo. Esto reduce fricción y mejora la moral del equipo.
        </div>
      </div>

      <script>
        // Calcular horas automáticamente de los turnos
        setTimeout(() => {
          let totalHoras = 0;
          wizardData.turnos.forEach(turno => {
            const [h1, m1] = turno.hora_inicio.split(':').map(Number);
            const [h2, m2] = turno.hora_fin.split(':').map(Number);
            let minutos = 0;

            if (turno.cruza_medianoche) {
              minutos = (24 * 60 - (h1 * 60 + m1)) + (h2 * 60 + m2);
            } else {
              minutos = (h2 * 60 + m2) - (h1 * 60 + m1);
            }

            const horasPorDia = minutos / 60;
            totalHoras += horasPorDia * turno.dias.length;
          });

          const elem = document.getElementById('horas-calculadas');
          if (elem) {
            elem.innerHTML = totalHoras > 0
              ? '✓ Calculado automáticamente: <strong>' + totalHoras.toFixed(1) + ' hrs/semana</strong>'
              : 'Configura turnos primero para calcular automáticamente';
          }
        }, 100);
      </script>
    </div>
  `;
}


// ============================================
// PASO 4: REVISAR Y CONFIRMAR
// ============================================

function renderPaso4() {
  // Calcular total de horas
  let totalHoras = 0;
  wizardData.turnos.forEach(turno => {
    const [h1, m1] = turno.hora_inicio.split(':').map(Number);
    const [h2, m2] = turno.hora_fin.split(':').map(Number);
    let minutos = 0;

    if (turno.cruza_medianoche) {
      minutos = (24 * 60 - (h1 * 60 + m1)) + (h2 * 60 + m2);
    } else {
      minutos = (h2 * 60 + m2) - (h1 * 60 + m1);
    }

    totalHoras += (minutos / 60) * turno.dias.length;
  });

  // Renderizar turnos
  const diasMapP4 = {1:'Lun', 2:'Mar', 3:'Mié', 4:'Jue', 5:'Vie', 6:'Sáb', 7:'Dom'};
  const mBadgeLabelsP4 = { presencial: '🏢 Presencial', remoto: '🏠 Teletrabajo', hibrido: '🔄 Híbrido' };
  let turnosHTML = '';
  wizardData.turnos.forEach((turno, index) => {
    const diasLabels = turno.dias.map(d => diasMapP4[d]).join(', ');
    const modalidad  = turno.modalidad || 'presencial';
    let geoResumen   = '';
    if (turno.requiere_geo && modalidad !== 'remoto') {
      geoResumen = `<div style="font-size:11px; color:var(--c-accent); margin-top:4px; font-weight:500;">
        📍 ${turno.geo_nombre_lugar || '(sin nombre de lugar)'} &middot; Radio: ${turno.geo_radio_metros || 100}m${turno.geo_modo_estricto ? ' &middot; 🔒 Estricto' : ''}
      </div>`;
    }
    turnosHTML += `
      <div style="padding:var(--space-3); background:var(--perf-bg); border-radius:8px; margin-bottom:var(--space-2);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-1);">
          <div style="font-size:13px; font-weight:600;">📅 ${turno.nombre_turno}</div>
          <span style="font-size:11px; padding:2px 8px; background:white; border:1px solid var(--perf-border); border-radius:4px; font-weight:600;">${mBadgeLabelsP4[modalidad] || '🏢 Presencial'}</span>
        </div>
        <div style="font-size:12px; opacity:0.8;">
          ${diasLabels}: ${turno.hora_inicio} – ${turno.hora_fin}${turno.cruza_medianoche ? ' 🌙' : ''}
        </div>
        ${geoResumen}
      </div>`;
  });

  return `
    <div>
      <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 var(--space-3); color: var(--c-secondary);">
        📋 Resumen de la Jornada
      </h3>
      <p style="color: var(--c-body); opacity: 0.7; margin: 0 0 var(--space-5); font-size: 14px;">
        Revisa todos los detalles antes de guardar
      </p>

      <div style="margin-bottom: var(--space-5);">
        <div style="font-size: 13px; font-weight: 600; opacity: 0.6; margin-bottom: var(--space-2); text-transform: uppercase;">
          Información básica
        </div>
        <div style="
          padding: var(--space-4);
          background: var(--perf-bg);
          border-radius: 8px;
        ">
          <div style="margin-bottom: var(--space-2);">
            <strong>Nombre:</strong> ${wizardData.nombre || '(Sin nombre)'}
          </div>
          ${wizardData.codigo_corto ? `
            <div style="margin-bottom: var(--space-2);">
              <strong>Código:</strong> ${wizardData.codigo_corto}
            </div>
          ` : ''}
          <div style="margin-bottom: var(--space-2);">
            <strong>Tipo:</strong> <span style="text-transform: capitalize;">${wizardData.tipo_jornada}</span>
          </div>
          ${wizardData.descripcion ? `
            <div>
              <strong>Descripción:</strong> ${wizardData.descripcion}
            </div>
          ` : ''}
        </div>
      </div>

      <div style="margin-bottom: var(--space-5);">
        <div style="font-size: 13px; font-weight: 600; opacity: 0.6; margin-bottom: var(--space-2); text-transform: uppercase;">
          Turnos configurados
        </div>
        ${turnosHTML}
      </div>

      <div style="margin-bottom: var(--space-5);">
        <div style="font-size: 13px; font-weight: 600; opacity: 0.6; margin-bottom: var(--space-2); text-transform: uppercase;">
          Políticas
        </div>
        <div style="
          padding: var(--space-4);
          background: var(--perf-bg);
          border-radius: 8px;
        ">
          <div style="margin-bottom: var(--space-2);">
            ⏱️ <strong>${totalHoras.toFixed(1)} hrs/semana</strong>
          </div>
          <div style="margin-bottom: var(--space-2);">
            🔔 Tolerancia: ${wizardData.tolerancia_entrada_min} min entrada / ${wizardData.tolerancia_salida_min} min salida
          </div>
        </div>
      </div>

      <div style="
        padding: var(--space-4);
        background: var(--c-accent)10;
        border: 1px solid var(--c-accent)30;
        border-radius: 8px;
        text-align: center;
      ">
        <div style="font-size: 14px;">
          ✅ Todo listo para crear la jornada
        </div>
      </div>
    </div>
  `;
}


// ============================================
// VALIDACIONES Y ENVÍO
// ============================================

function validarPasoActual() {
  switch (wizardCurrentStep) {
    case 1:
      const nombre = document.getElementById('wizard-nombre').value.trim();
      if (!nombre) {
        alert('Por favor ingresa un nombre para la jornada');
        return false;
      }
      return true;

    case 2:
      if (wizardData.turnos.length === 0) {
        alert('Debes configurar al menos un turno');
        return false;
      }
      for (let i = 0; i < wizardData.turnos.length; i++) {
        const turno = wizardData.turnos[i];
        if (turno.dias.length === 0) {
          alert(`Turno ${i + 1}: debes seleccionar al menos un día`);
          return false;
        }
        if (turno.requiere_geo && (turno.modalidad || 'presencial') !== 'remoto') {
          if (!turno.geo_nombre_lugar || !turno.geo_nombre_lugar.trim()) {
            alert(`Turno ${i + 1}: el nombre del lugar es obligatorio cuando la geolocalización está activa.`);
            return false;
          }
          if (!turno.geo_lat || !turno.geo_lng) {
            alert(`Turno ${i + 1}: debes definir las coordenadas del lugar de trabajo.\nUsa el botón "📍 Usar mi ubicación" si estás allí, o introdúcelas manualmente.`);
            return false;
          }
        }
      }
      return true;

    case 3:
      return true;

    case 4:
      return true;

    default:
      return true;
  }
}

function guardarDatosPaso() {
  switch (wizardCurrentStep) {
    case 1:
      wizardData.nombre = document.getElementById('wizard-nombre').value.trim();
      wizardData.descripcion = document.getElementById('wizard-descripcion').value.trim();
      wizardData.codigo_corto = document.getElementById('wizard-codigo').value.trim();
      wizardData.color_hex = document.getElementById('wizard-color').value;
      wizardData.tipo_jornada = document.querySelector('input[name="tipo_jornada"]:checked').value;
      break;

    case 2:
      // Los turnos ya están siendo actualizados en tiempo real
      break;

    case 3:
      wizardData.tolerancia_entrada_min = parseInt(document.getElementById('wizard-tolerancia-entrada').value) || 15;
      wizardData.tolerancia_salida_min = parseInt(document.getElementById('wizard-tolerancia-salida').value) || 5;
      wizardData.horas_semanales_esperadas = parseFloat(document.getElementById('wizard-horas-semanales').value) || 40;
      break;
  }
}

function submitCrearJornada() {
  // Preparar datos para enviar
  const formData = new FormData();
  formData.append('action', 'crear_jornada');
  formData.append('nombre', wizardData.nombre);
  formData.append('descripcion', wizardData.descripcion);
  formData.append('codigo_corto', wizardData.codigo_corto);
  formData.append('tipo_jornada', wizardData.tipo_jornada);
  formData.append('horas_semanales_esperadas', wizardData.horas_semanales_esperadas);
  formData.append('tolerancia_entrada_min', wizardData.tolerancia_entrada_min);
  formData.append('tolerancia_salida_min', wizardData.tolerancia_salida_min);
  formData.append('color_hex', wizardData.color_hex);
  formData.append('turnos', JSON.stringify(wizardData.turnos));

  // Enviar al backend
  fetch('a-desempeno-dashboard.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert('¡Jornada creada exitosamente! 🎉');
      cerrarModalJornada();
      location.reload(); // Recargar para ver la nueva jornada
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert('Error al guardar la jornada');
  });
}


// ============================================
// MODAL: ASIGNAR EMPLEADOS
// ============================================

function abrirModalAsignarEmpleados(jornadaId, jornadaNombre) {
  document.getElementById('asignar-jornada-id').value = jornadaId;
  document.getElementById('asignar-jornada-nombre').textContent = `Jornada: ${jornadaNombre}`;

  // Cargar lista de empleados
  cargarEmpleadosParaAsignar(jornadaId);

  document.getElementById('modalAsignarEmpleados').style.display = 'flex';
}

function cerrarModalAsignar() {
  document.getElementById('modalAsignarEmpleados').style.display = 'none';
}

function cargarEmpleadosParaAsignar(jornadaId) {
  // Obtener empleados desde el backend (puedes usar una variable global PHP o hacer AJAX)
  // Por ahora simulamos que tenemos la variable global `equipoData`

  const container = document.getElementById('lista-empleados-asignar');
  container.innerHTML = '<div style="text-align: center; padding: var(--space-4);">Cargando empleados...</div>';

  // Aquí deberías hacer un fetch al backend para obtener empleados
  // Por simplicidad, asumimos que ya tienes los datos en el frontend

  setTimeout(() => {
    // Ejemplo de renderizado
    container.innerHTML = `
      <div style="max-height: 300px; overflow-y: auto;">
        <p style="font-size: 13px; opacity: 0.7; margin-bottom: var(--space-3);">
          💡 Selecciona los empleados que quieres asignar a esta jornada
        </p>
        <!-- Aquí irían los empleados dinámicamente -->
        <div style="padding: var(--space-3); text-align: center; opacity: 0.5;">
          Implementar lista de empleados con checkboxes
        </div>
      </div>
    `;
  }, 500);
}

function submitAsignarEmpleados(event) {
  event.preventDefault();

  const jornadaId = document.getElementById('asignar-jornada-id').value;
  const fechaInicio = document.getElementById('asignar-fecha-inicio').value;
  const fechaFin = document.getElementById('asignar-fecha-fin').value;

  // Obtener empleados seleccionados
  const checkboxes = document.querySelectorAll('#lista-empleados-asignar input[type="checkbox"]:checked');
  const empleados = Array.from(checkboxes).map(cb => ({
    persona_id: cb.value,
    fecha_inicio: fechaInicio,
    fecha_fin: fechaFin || null,
    notas: ''
  }));

  if (empleados.length === 0) {
    alert('Selecciona al menos un empleado');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'asignar_empleados');
  formData.append('jornada_id', jornadaId);
  formData.append('empleados', JSON.stringify(empleados));

  fetch('a-desempeno-dashboard.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      cerrarModalAsignar();
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert('Error al asignar empleados');
  });
}


// ============================================
// MODAL: DETALLES Y EDITAR JORNADA
// ============================================

let jornadaActualId = null;

function verDetallesJornada(jornadaId) {
  jornadaActualId = jornadaId;
  document.getElementById('modalDetallesJornada').style.display = 'flex';

  // Mostrar loading
  const content = document.getElementById('detalles-jornada-content');
  content.innerHTML = `
    <div style="text-align: center; padding: var(--space-8);">
      <div class="spinner" style="
        width: 40px;
        height: 40px;
        border: 3px solid var(--perf-border);
        border-top-color: var(--c-accent);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto var(--space-4);
      "></div>
      <p>Cargando detalles...</p>
    </div>
  `;

  // Cargar datos de la jornada
  fetch(`a-desempeno-dashboard.php?action=get_jornada&jornada_id=${jornadaId}`)
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        renderDetallesJornada(data.jornada, data.turnos, data.empleados_asignados);
      } else {
        content.innerHTML = `
          <div style="text-align: center; padding: var(--space-6); color: #FF3B6D;">
            <p>Error: ${data.message}</p>
          </div>
        `;
      }
    })
    .catch(err => {
      console.error(err);
      content.innerHTML = `
        <div style="text-align: center; padding: var(--space-6); color: #FF3B6D;">
          <p>Error al cargar los datos</p>
        </div>
      `;
    });
}

function cerrarModalDetalles() {
  document.getElementById('modalDetallesJornada').style.display = 'none';
  jornadaActualId = null;
}

function renderDetallesJornada(jornada, turnos, empleadosAsignados) {
  const content = document.getElementById('detalles-jornada-content');
  document.getElementById('detalles-jornada-titulo').textContent = jornada.nombre;

  // Agrupar turnos por nombre + horas
  const turnosAgrupados = {};
  turnos.forEach(t => {
    const key = `${t.nombre_turno}_${t.hora_inicio}_${t.hora_fin}`;
    if (!turnosAgrupados[key]) {
      turnosAgrupados[key] = {
        nombre:           t.nombre_turno,
        hora_inicio:      t.hora_inicio,
        hora_fin:         t.hora_fin,
        cruza_medianoche: t.cruza_medianoche,
        modalidad:        t.modalidad        || 'presencial',
        requiere_geo:     t.requiere_geo     || 0,
        geo_nombre_lugar: t.geo_nombre_lugar || '',
        geo_radio_metros: t.geo_radio_metros || 100,
        geo_modo_estricto:t.geo_modo_estricto|| 0,
        dias: []
      };
    }
    turnosAgrupados[key].dias.push(t.dia_semana);
  });

  const diasMap = {1:'Lun', 2:'Mar', 3:'Mié', 4:'Jue', 5:'Vie', 6:'Sáb', 7:'Dom'};
  const mBadgeDet = { presencial: '🏢 Presencial', remoto: '🏠 Teletrabajo', hibrido: '🔄 Híbrido' };

  // Renderizar turnos
  let turnosHTML = '';
  Object.values(turnosAgrupados).forEach(turno => {
    const diasLabels = turno.dias.sort((a,b) => a-b).map(d => diasMap[d]).join(', ');
    const modalidad  = turno.modalidad || 'presencial';
    let geoInfo = '';
    if (turno.requiere_geo && modalidad !== 'remoto') {
      geoInfo = `<div style="font-size:11px; color:var(--c-accent); margin-top:4px; font-weight:500;">
        📍 ${turno.geo_nombre_lugar || 'Geo activo'} &middot; ${turno.geo_radio_metros || 100}m${turno.geo_modo_estricto ? ' &middot; 🔒 Estricto' : ''}
      </div>`;
    }
    turnosHTML += `
      <div style="padding:var(--space-3); background:var(--perf-bg); border-radius:8px; margin-bottom:var(--space-2); font-size:14px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
          <strong>${turno.nombre}</strong>
          <span style="font-size:11px; padding:2px 8px; background:white; border:1px solid var(--perf-border); border-radius:4px; font-weight:600;">${mBadgeDet[modalidad] || '🏢 Presencial'}</span>
        </div>
        <span style="opacity:0.8;">${diasLabels}: ${turno.hora_inicio.slice(0,5)} – ${turno.hora_fin.slice(0,5)}</span>
        ${turno.cruza_medianoche ? ' <span title="Turno nocturno">🌙</span>' : ''}
        ${geoInfo}
      </div>`;
  });

  // Renderizar empleados asignados
  let empleadosHTML = '';
  if (empleadosAsignados.length === 0) {
    empleadosHTML = `
      <div style="
        text-align: center;
        padding: var(--space-6);
        background: var(--perf-bg);
        border-radius: 8px;
        color: var(--c-body);
        opacity: 0.6;
      ">
        <p style="margin: 0;">No hay empleados asignados a esta jornada</p>
        <button
          onclick="cerrarModalDetalles(); abrirModalAsignarEmpleados(${jornada.id}, '${jornada.nombre.replace(/'/g, "\\'")}')"
          style="
            margin-top: var(--space-3);
            padding: 8px 16px;
            background: var(--c-accent);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
          "
        >
          + Asignar Empleados
        </button>
      </div>
    `;
  } else {
    empleadosAsignados.forEach(emp => {
      const fechaInicio = emp.fecha_inicio ? new Date(emp.fecha_inicio).toLocaleDateString('es-ES') : '-';
      const fechaFin = emp.fecha_fin ? new Date(emp.fecha_fin).toLocaleDateString('es-ES') : 'Indefinido';

      empleadosHTML += `
        <div class="empleado-asignado-item" style="
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: var(--space-3);
          background: white;
          border: 1px solid var(--perf-border);
          border-radius: 8px;
          margin-bottom: var(--space-2);
        ">
          <div style="flex: 1;">
            <div style="font-weight: 600; font-size: 14px; color: var(--c-secondary);">
              ${emp.nombre_persona}
            </div>
            <div style="font-size: 12px; opacity: 0.7;">
              ${emp.cargo || 'Sin cargo'} &bull; Desde: ${fechaInicio} ${emp.fecha_fin ? '&bull; Hasta: ' + fechaFin : ''}
            </div>
          </div>
          <button
            onclick="confirmarDesasignarEmpleado(${emp.id}, '${emp.nombre_persona.replace(/'/g, "\\'")}')"
            style="
              padding: 8px 12px;
              background: white;
              border: 1px solid #FF3B6D;
              border-radius: 6px;
              color: #FF3B6D;
              font-size: 12px;
              font-weight: 600;
              cursor: pointer;
              transition: all 0.2s ease;
              white-space: nowrap;
            "
            onmouseover="this.style.background='#FF3B6D'; this.style.color='white'"
            onmouseout="this.style.background='white'; this.style.color='#FF3B6D'"
            title="Desvincular de esta jornada"
          >
            Desvincular
          </button>
        </div>
      `;
    });
  }

  content.innerHTML = `
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6);">

      <!-- Columna izquierda: Info de la jornada -->
      <div>
        <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 var(--space-4); color: var(--c-secondary);">
          Información de la Jornada
        </h3>

        <!-- Info básica -->
        <div style="
          padding: var(--space-4);
          background: var(--perf-bg);
          border-radius: 12px;
          margin-bottom: var(--space-4);
          border-left: 4px solid ${jornada.color_hex || '#184656'};
        ">
          <div style="display: grid; gap: var(--space-2); font-size: 14px;">
            <div>
              <span style="opacity: 0.6;">Tipo:</span>
              <strong style="text-transform: capitalize;">${jornada.tipo_jornada}</strong>
            </div>
            ${jornada.codigo_corto ? `
            <div>
              <span style="opacity: 0.6;">Código:</span>
              <strong>${jornada.codigo_corto}</strong>
            </div>
            ` : ''}
            <div>
              <span style="opacity: 0.6;">Horas/semana:</span>
              <strong>${parseFloat(jornada.horas_semanales_esperadas).toFixed(1)} hrs</strong>
            </div>
            <div>
              <span style="opacity: 0.6;">Tolerancia entrada:</span>
              <strong>${jornada.tolerancia_entrada_min} min</strong>
            </div>
            <div>
              <span style="opacity: 0.6;">Tolerancia salida:</span>
              <strong>${jornada.tolerancia_salida_min} min</strong>
            </div>
            <div>
              <span style="opacity: 0.6;">Estado:</span>
              ${jornada.is_active == 1
                ? '<span style="color: #00D98F; font-weight: 600;">Activa</span>'
                : '<span style="color: #94A3B8; font-weight: 600;">Archivada</span>'}
            </div>
          </div>
        </div>

        <!-- Turnos -->
        <h4 style="font-size: 14px; font-weight: 600; margin: 0 0 var(--space-3); opacity: 0.7;">
          Turnos Configurados
        </h4>
        ${turnosHTML}

        ${jornada.descripcion ? `
        <div style="margin-top: var(--space-4);">
          <h4 style="font-size: 14px; font-weight: 600; margin: 0 0 var(--space-2); opacity: 0.7;">
            Descripción
          </h4>
          <p style="font-size: 13px; margin: 0; opacity: 0.8;">${jornada.descripcion}</p>
        </div>
        ` : ''}
      </div>

      <!-- Columna derecha: Empleados asignados -->
      <div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-4);">
          <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--c-secondary);">
            Empleados Asignados (${empleadosAsignados.length})
          </h3>
          ${empleadosAsignados.length > 0 ? `
          <button
            onclick="cerrarModalDetalles(); abrirModalAsignarEmpleados(${jornada.id}, '${jornada.nombre.replace(/'/g, "\\'")}')"
            style="
              padding: 6px 12px;
              background: var(--c-accent);
              border: none;
              border-radius: 6px;
              color: white;
              font-size: 12px;
              font-weight: 600;
              cursor: pointer;
            "
          >
            + Agregar
          </button>
          ` : ''}
        </div>

        <div style="max-height: 400px; overflow-y: auto;">
          ${empleadosHTML}
        </div>

        <!-- Nota informativa -->
        <div style="
          margin-top: var(--space-4);
          padding: var(--space-3);
          background: #FFF3CD;
          border: 1px solid #FFECB5;
          border-radius: 8px;
          font-size: 12px;
          color: #856404;
        ">
          <strong>Nota:</strong> Una persona puede estar asignada a múltiples jornadas.
          Al desvincular, se finaliza la asignación actual pero se mantiene el histórico.
        </div>
      </div>
    </div>
  `;
}

function confirmarDesasignarEmpleado(asignacionId, nombreEmpleado) {
  if (confirm(`¿Estás seguro de desvincular a "${nombreEmpleado}" de esta jornada?\n\nLa asignación finalizará con fecha de hoy. El histórico se mantendrá.`)) {
    desasignarEmpleado(asignacionId);
  }
}

function desasignarEmpleado(asignacionId) {
  const formData = new FormData();
  formData.append('action', 'desasignar_empleado');
  formData.append('asignacion_id', asignacionId);
  formData.append('fecha_fin', new Date().toISOString().split('T')[0]); // Hoy

  fetch('a-desempeno-dashboard.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert('Empleado desvinculado exitosamente');
      // Recargar el modal
      if (jornadaActualId) {
        verDetallesJornada(jornadaActualId);
      }
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert('Error al desvincular empleado');
  });
}


// ============================================
// OTRAS FUNCIONES
// ============================================

function confirmarEliminarJornada(jornadaId, jornadaNombre) {
  if (confirm(`¿Estás seguro de eliminar la jornada "${jornadaNombre}"?\n\nEsta acción no se puede deshacer.`)) {
    eliminarJornada(jornadaId);
  }
}

function eliminarJornada(jornadaId) {
  const formData = new FormData();
  formData.append('action', 'eliminar_jornada');
  formData.append('jornada_id', jornadaId);

  fetch('a-desempeno-dashboard.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert('Error al eliminar');
  });
}

function filtrarEmpleados(query) {
  const items = document.querySelectorAll('#lista-empleados-asignar .empleado-item');
  items.forEach(item => {
    const nombre = item.dataset.nombre.toLowerCase();
    item.style.display = nombre.includes(query.toLowerCase()) ? 'block' : 'none';
  });
}


// ============================================
// GEO-FICHAJE: HELPERS DEL WIZARD
// ============================================

function actualizarModalidadTurno(index, modalidad) {
  actualizarTurno(index, 'modalidad', modalidad);
  // Si cambia a Teletrabajo, forzar geo desactivado
  if (modalidad === 'remoto') {
    actualizarTurno(index, 'requiere_geo', 0);
  }
  renderWizardStep();
}

function toggleGeoTurno(index, checked) {
  actualizarTurno(index, 'requiere_geo', checked ? 1 : 0);
  // Mostrar/ocultar campos sin re-renderizar (preserva foco)
  const fields = document.getElementById('geo-fields-' + index);
  if (fields) fields.style.display = checked ? 'block' : 'none';
  // Actualizar borde del toggle label
  const label = fields ? fields.previousElementSibling : null;
  if (label) {
    label.style.borderColor = checked ? 'var(--c-accent)' : 'var(--perf-border)';
    label.style.background  = checked ? 'rgba(239,127,27,0.04)' : 'white';
  }
}

function usarUbicacionActual(index, ev) {
  if (!navigator.geolocation) {
    alert('Tu navegador no soporta geolocalización.');
    return;
  }
  const btn = ev ? ev.currentTarget : null;
  const originalText = btn ? btn.textContent : '';
  if (btn) { btn.textContent = '⏳ Obteniendo...'; btn.disabled = true; }

  navigator.geolocation.getCurrentPosition(
    function(pos) {
      const lat = pos.coords.latitude.toFixed(7);
      const lng = pos.coords.longitude.toFixed(7);
      actualizarTurno(index, 'geo_lat', parseFloat(lat));
      actualizarTurno(index, 'geo_lng', parseFloat(lng));
      const latInput = document.getElementById('geo-lat-' + index);
      const lngInput = document.getElementById('geo-lng-' + index);
      if (latInput) latInput.value = lat;
      if (lngInput) lngInput.value = lng;
      if (btn) {
        btn.textContent = '✅ Capturada';
        btn.style.background = '#00D98F';
        setTimeout(function() {
          btn.textContent = originalText;
          btn.style.background = '';
          btn.disabled = false;
        }, 2500);
      }
    },
    function(err) {
      if (btn) { btn.textContent = originalText; btn.disabled = false; }
      const msgs = {
        1: 'Permiso denegado. Activa la ubicación en tu navegador.',
        2: 'Ubicación no disponible en este momento.',
        3: 'Tiempo de espera agotado. Inténtalo de nuevo.'
      };
      alert('📍 ' + (msgs[err.code] || 'Error al obtener la ubicación.'));
    },
    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
  );
}
