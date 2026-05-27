<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/mailer.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  http_response_code(405);
  header('Allow: POST');
  exit;
}

$returnTo = safe_return_to((string) ($_POST['return_to'] ?? '/es/'));
$status = '0';

if (!empty($_POST['company'])) {
  redirect_with_status($returnTo, '1');
}

$csrf = (string) ($_POST['csrf'] ?? $_POST['_token'] ?? '');
if ($csrf === '' || empty($_SESSION['csrf']) || !hash_equals((string) $_SESSION['csrf'], $csrf)) {
  redirect_with_status($returnTo, '0');
}

$data = [
  'name' => trim((string) ($_POST['name'] ?? '')),
  'phone' => trim((string) ($_POST['phone'] ?? '')),
  'email' => trim((string) ($_POST['email'] ?? '')),
  'service' => trim((string) ($_POST['service'] ?? '')),
  'message' => trim((string) ($_POST['message'] ?? '')),
  'path' => $returnTo,
];

if ($data['name'] === '' || $data['phone'] === '') {
  redirect_with_status($returnTo, '0');
}

$logged = log_contact($data);
$mailed = false;
try {
  $mailed = send_contact($data);
} catch (Throwable $e) {
  error_log('Contact mail error: ' . $e->getMessage());
}

$status = ($logged || $mailed) ? '1' : '0';
redirect_with_status($returnTo, $status);

function log_contact(array $data): bool {
  $dir = getenv('CONTACT_LOG_DIR') ?: (__DIR__ . '/../storage/logs');
  if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
    return false;
  }

  $line = json_encode([
    'ts' => date('c'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'name' => $data['name'] ?? '',
    'phone' => $data['phone'] ?? '',
    'email' => $data['email'] ?? '',
    'service' => $data['service'] ?? '',
    'message' => str_replace(["\r", "\n"], ' ', (string) ($data['message'] ?? '')),
    'path' => $data['path'] ?? '',
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

  return @file_put_contents(rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . 'contacts.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}

function safe_return_to(string $value): string {
  $value = trim($value);
  if ($value === '' || !str_starts_with($value, '/') || str_starts_with($value, '//')) {
    return '/es/';
  }
  if (preg_match('/[\r\n]/', $value)) {
    return '/es/';
  }
  return $value;
}

function redirect_with_status(string $returnTo, string $status): void {
  $hash = '';
  $path = $returnTo;
  if (($hashPos = strpos($returnTo, '#')) !== false) {
    $hash = substr($returnTo, $hashPos);
    $path = substr($returnTo, 0, $hashPos);
  }
  $separator = str_contains($path, '?') ? '&' : '?';
  header('Location: ' . $path . $separator . 'sent=' . rawurlencode($status) . $hash, true, 303);
  exit;
}
