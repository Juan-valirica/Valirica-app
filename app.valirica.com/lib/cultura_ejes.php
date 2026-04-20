<?php
// lib/cultura_ejes.php
// v2: usa las 6 dimensiones Hofstede para calcular X/Y
// Entrada esperada: valores en 0..100 (si tus datos usan otra escala, avísame para ajustar)
// Retorna X,Y en escala -5..+5

function norm_0_100_to_m1_1($v) {
    if (!is_numeric($v)) return 0.0;
    $v = (float)$v;
    if ($v < 0) $v = 0;
    if ($v > 100) $v = 100;
    return ($v / 100.0) * 2.0 - 1.0; // -1 .. +1
}

function clamp($v, $min = -1.0, $max = 1.0) {
    if ($v < $min) return $min;
    if ($v > $max) return $max;
    return $v;
}

function calcula_ejes_hofstede_v2(array $v, array $opts = []): array {
    // Normalizar (0..100 -> -1..+1) - valores por defecto 50 -> 0
    $ind = norm_0_100_to_m1_1($v['individualismo'] ?? 50.0);
    $mas = norm_0_100_to_m1_1($v['masculinidad'] ?? 50.0);
    $inc = norm_0_100_to_m1_1($v['incertidumbre'] ?? 50.0);
    $pwr = norm_0_100_to_m1_1($v['distancia_poder'] ?? 50.0);
    $lto = norm_0_100_to_m1_1($v['largo_plazo'] ?? 50.0);
    $indul = norm_0_100_to_m1_1($v['indulgencia'] ?? 50.0);

    // Pesos recomendados (ajustables)
    $weightsX = $opts['weights']['X'] ?? [
        'individualismo' => 0.35,
        'masculinidad'   => 0.20,
        'distancia_poder'=> -0.25, // negativo: más poder -> más interno
        'largo_plazo'    => -0.10  // penaliza hacia interno si es largo plazo
    ];
    $weightsY = $opts['weights']['Y'] ?? [
        'indulgencia'    => 0.40,
        'incertidumbre'  => -0.35, // negativo: más aversión -> más control
        'masculinidad'   => 0.10,
        'largo_plazo'    => -0.15
    ];

    // Sumar ponderada
    $X = 0.0;
    $X += ($weightsX['individualismo'] ?? 0.0) * $ind;
    $X += ($weightsX['masculinidad']   ?? 0.0) * $mas;
    $X += ($weightsX['distancia_poder']?? 0.0) * $pwr;
    $X += ($weightsX['largo_plazo']    ?? 0.0) * $lto;

    $Y = 0.0;
    $Y += ($weightsY['indulgencia']    ?? 0.0) * $indul;
    $Y += ($weightsY['incertidumbre']  ?? 0.0) * $inc;
    $Y += ($weightsY['masculinidad']   ?? 0.0) * $mas;
    $Y += ($weightsY['largo_plazo']    ?? 0.0) * $lto;

    // Evitar >1 si pesos suman alto: normalizar por suma absoluta
    $sumAbsX = abs($weightsX['individualismo'] ?? 0) + abs($weightsX['masculinidad'] ?? 0) + abs($weightsX['distancia_poder'] ?? 0) + abs($weightsX['largo_plazo'] ?? 0);
    $sumAbsY = abs($weightsY['indulgencia'] ?? 0) + abs($weightsY['incertidumbre'] ?? 0) + abs($weightsY['masculinidad'] ?? 0) + abs($weightsY['largo_plazo'] ?? 0);
    if ($sumAbsX > 1.0) $X = $X / $sumAbsX;
    if ($sumAbsY > 1.0) $Y = $Y / $sumAbsY;

    // Clamp y escalar a -5..+5
    $X = clamp($X, -1.0, 1.0) * 5.0;
    $Y = clamp($Y, -1.0, 1.0) * 5.0;

    return [ round($X, 2), round($Y, 2) ];
}

function calcula_ejes_batch(array $rows, array $opts = []): array {
    $out = [];
    foreach ($rows as $r) {
        $v = [
            'individualismo' => $r['hofstede_individualismo'] ?? ($r['individualismo'] ?? 50.0),
            'masculinidad'   => $r['hofstede_resultados'] ?? ($r['masculinidad'] ?? 50.0),
            'incertidumbre'  => $r['hofstede_incertidumbre'] ?? ($r['incertidumbre'] ?? 50.0),
            'distancia_poder'=> $r['hofstede_poder'] ?? ($r['distancia_poder'] ?? 50.0),
            'largo_plazo'    => $r['hofstede_largo_plazo'] ?? ($r['largo_plazo'] ?? 50.0),
            'indulgencia'    => $r['hofstede_espontaneidad'] ?? ($r['indulgencia'] ?? 50.0)
        ];
        list($x,$y) = calcula_ejes_hofstede_v2($v, $opts);
        $label = trim(($r['nombre_persona'] ?? '') . ' ' . ($r['apellido'] ?? ''));
        $out[] = ['id' => $r['id'] ?? null, 'x'=>$x, 'y'=>$y, 'label' => $label];
    }
    return $out;
}
