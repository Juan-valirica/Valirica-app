<?php
/**
 * Catálogo central de Quick Wins Hofstede
 * Cada dimensión tiene dos variantes:
 * - equipo_mayor  → el equipo puntúa más alto que la cultura ideal
 * - equipo_menor  → el equipo puntúa más bajo que la cultura ideal
 */

return [

  /* =========================================================
   * 1. JERARQUÍA (Distancia al poder)
   * ========================================================= */
  'jerarquia' => [

    'equipo_mayor' => [
      'titulo' => 'Exceso de jerarquía',
      'que_pasa' => 'La empresa quiere una cultura más horizontal, pero el equipo espera instrucciones claras y validación constante. Esto frena la iniciativa porque las personas no se sienten autorizadas a decidir por sí mismas.',
      'quick_win' => 'Define zonas explícitas de autonomía. Aclara qué decisiones puede tomar el equipo sin pedir permiso y cuáles requieren validación. La evidencia muestra que la autonomía funciona cuando tiene límites claros.'
    ],

    'equipo_menor' => [
      'titulo' => 'Autoridad difusa',
      'que_pasa' => 'El equipo opera de forma muy horizontal, pero la empresa espera orden y decisiones claras. Esto genera confusión y ralentiza la ejecución.',
      'quick_win' => 'Aclara roles de decisión, no cargos. Define quién decide qué por tema o proyecto. La claridad en la toma de decisiones reduce fricción y acelera resultados.'
    ],

  ],

  /* =========================================================
   * 2. TRABAJO EN EQUIPO VS. AUTONOMÍA
   * ========================================================= */
  'equipo_vs_autonomia' => [

    'equipo_mayor' => [
      'titulo' => 'Islas de talento',
      'que_pasa' => 'Hay personas muy capaces, pero cada una trabaja de forma aislada. La empresa busca colaboración, pero el sistema premia resultados individuales.',
      'quick_win' => 'Introduce objetivos compartidos reales. Cuando el éxito depende del resultado colectivo, la colaboración aparece de forma natural.'
    ],

    'equipo_menor' => [
      'titulo' => 'Exceso de consenso',
      'que_pasa' => 'El equipo prioriza tanto el acuerdo que las decisiones se vuelven lentas. Nadie quiere incomodar, aunque eso frene el avance.',
      'quick_win' => 'Normaliza el desacuerdo estructurado. Define espacios donde cuestionar decisiones sea obligatorio para mejorar la calidad de las decisiones.'
    ],

  ],

  /* =========================================================
   * 3. MANEJO DE LA INCERTIDUMBRE
   * ========================================================= */
  'incertidumbre' => [

    'equipo_mayor' => [
      'titulo' => 'Miedo al error',
      'que_pasa' => 'La empresa pide innovación, pero el equipo necesita certezas. Cada error se vive como una amenaza, lo que bloquea la iniciativa.',
      'quick_win' => 'Diseña experimentos pequeños y seguros. Limita el riesgo y aclara consecuencias. La seguridad psicológica aumenta cuando el error es controlado.'
    ],

    'equipo_menor' => [
      'titulo' => 'Caos creativo',
      'que_pasa' => 'El equipo experimenta constantemente, pero la empresa necesita estabilidad. Se lanzan ideas sin estructura clara.',
      'quick_win' => 'Cierra cada experimento con aprendizajes documentados. La innovación sostenible necesita ciclos claros de aprendizaje.'
    ],

  ],

  /* =========================================================
   * 4. RESULTADOS VS. BIENESTAR
   * ========================================================= */
  'resultados_vs_bienestar' => [

    'equipo_mayor' => [
      'titulo' => 'Rendimiento extremo',
      'que_pasa' => 'El equipo empuja fuerte por resultados, pero empieza a mostrar señales de desgaste. El bienestar queda en segundo plano.',
      'quick_win' => 'Incluye métricas de sostenibilidad del rendimiento, no solo resultados. Equipos que miden energía rinden mejor a largo plazo.'
    ],

    'equipo_menor' => [
      'titulo' => 'Confort sin tracción',
      'que_pasa' => 'El clima es bueno, pero los resultados no avanzan al ritmo esperado. Se evita la incomodidad necesaria para crecer.',
      'quick_win' => 'Define retos claros y medibles sin romper el clima. La exigencia con sentido impulsa el rendimiento.'
    ],

  ],

  /* =========================================================
   * 5. MIRADA DE LARGO PLAZO
   * ========================================================= */
  'largo_plazo' => [

    'equipo_mayor' => [
      'titulo' => 'Visión sin cierre',
      'que_pasa' => 'El equipo piensa estratégicamente, pero le cuesta cerrar ciclos. Las ideas se quedan en planes.',
      'quick_win' => 'Divide la visión en hitos cortos y visibles. Los logros intermedios mantienen foco y ejecución.'
    ],

    'equipo_menor' => [
      'titulo' => 'Urgencia constante',
      'que_pasa' => 'El equipo vive apagando fuegos y no logra conectar su trabajo con una visión de futuro.',
      'quick_win' => 'Conecta cada proyecto con impacto futuro visible. Entender el para qué aumenta el compromiso.'
    ],

  ],

  /* =========================================================
   * 6. FLEXIBILIDAD VS. ESTRUCTURA
   * ========================================================= */
'flexibilidad_vs_estructura' => [

  // Equipo MÁS estructurado / restrictivo que la cultura ideal
  'equipo_mayor' => [
    'titulo' => 'Rigidez excesiva',
    'que_pasa' => 'La empresa quiere una cultura más flexible, pero el equipo necesita reglas claras y procesos definidos para sentirse seguro. Cuando todo queda abierto a interpretación, aparece ansiedad y bloqueo.',
    'quick_win' => 'Define reglas mínimas no negociables y deja libertad dentro de esos márgenes. La flexibilidad funciona cuando el equipo sabe exactamente dónde están los límites.'
  ],

  // Equipo MÁS flexible / indulgente que la cultura ideal
  'equipo_menor' => [
    'titulo' => 'Falta de estructura',
    'que_pasa' => 'El equipo se mueve con mucha libertad e improvisación, pero la empresa necesita mayor consistencia. Sin acuerdos claros, cada persona trabaja a su manera.',
    'quick_win' => 'Introduce estructuras ligeras: checklists simples, acuerdos básicos y formas estándar de trabajar. Pequeñas reglas bien definidas aumentan la calidad sin frenar la agilidad.'
  ],

],

];
