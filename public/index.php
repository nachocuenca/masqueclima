<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/seo.php';

// Rutas especiales: health, sitemap, robots
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
if ($uriPath === '/health') { header('Content-Type: text/plain'); echo 'OK'; exit; }
if ($uriPath === '/sitemap.xml') { include __DIR__.'/sitemap.php'; exit; }
if ($uriPath === '/robots.txt')  { include __DIR__.'/robots.php'; exit; }

// POST: formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
  csrf_check();
  $name    = trim($_POST['name'] ?? '');
  $phone   = trim($_POST['phone'] ?? '');
  $email   = trim($_POST['email'] ?? '');
  $service = trim($_POST['service'] ?? '');
  $message = trim($_POST['message'] ?? '');

  $dir = __DIR__ . '/../storage';
  if (!is_dir($dir)) @mkdir($dir, 0775, true);
  $line = date('c') . " | {$name} | {$phone} | {$email} | {$service} | " . str_replace(["\r","\n"], ' ', $message) . PHP_EOL;
  @file_put_contents($dir.'/contacts.log', $line, FILE_APPEND);

  flash('form_ok', (string) t('form.ok', 'Solicitud enviada. Te contactaremos pronto.'));
  header('Location: ' . $_SERVER['REQUEST_URI']);
  exit;
}

// Routing simple
$path = trim($uriPath, '/');
$segments = $path === '' ? [] : array_values(array_filter(explode('/', $path)));

$langs   = $GLOBALS['config']['brand']['langs'] ?? ['es'];
$default = $GLOBALS['config']['brand']['default_lang'] ?? 'es';
$current = $GLOBALS['current_lang'] ?? $default;

if (!empty($segments) && in_array($segments[0], $langs, true)) {
  $current = $segments[0];
  $GLOBALS['current_lang'] = $current;
  array_shift($segments);
}

$view = empty($segments) ? 'home' : '404';
$GLOBALS['view'] = $view;
include __DIR__ . '/../views/layout.php';
