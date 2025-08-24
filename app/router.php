<?php
declare(strict_types=1);

/**
 * $segments llega sin el prefijo de idioma (lo quita index.php).
 * $view debe ser NOMBRE RELATIVO (p.ej. 'home') para que lo cargue el layout.
 */
function route(array $segments = []): void {
  $first = $segments[0] ?? '';

  switch ($first) {
    case '':
      $view = 'home';                     // Home de prueba (abajo)
      include __DIR__ . '/../views/layout.php';
      break;

    default:
      http_response_code(404);
      $view = '404';
      include __DIR__ . '/../views/layout.php';
      break;
  }
}
