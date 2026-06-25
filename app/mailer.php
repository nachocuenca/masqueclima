<?php
function contact_recipients(array $brand): array {
  $raw = getenv('CONTACT_RECIPIENTS') ?: '';
  $candidates = $raw !== '' ? preg_split('/[,;]+/', $raw) : [];
  if (!is_array($candidates) || count($candidates) === 0) {
    $candidates = [(string)($brand['email'] ?? '')];
  }

  $recipients = [];
  foreach ($candidates as $candidate) {
    $email = trim((string)$candidate);
    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $recipients[$email] = $email;
    }
  }

  if (count($recipients) === 0 && !empty($brand['email']) && filter_var($brand['email'], FILTER_VALIDATE_EMAIL)) {
    $recipients[(string)$brand['email']] = (string)$brand['email'];
  }

  return array_values($recipients);
}

function contact_request_type_labels(): array {
  return [
    'no_indicado' => 'No indicado',
    'cliente_actual_incidencia_urgente' => 'Cliente actual / incidencia urgente',
    'nueva_instalacion_presupuesto' => 'Nueva instalación o presupuesto',
    'revision_mantenimiento' => 'Revisión o mantenimiento',
    'lista_espera' => 'Lista de espera',
  ];
}

function contact_normalize_request_type(string $value): string {
  $value = trim($value);
  if ($value === '' || preg_match('/[\x00-\x1F\x7F]/', $value) || strlen($value) > 80) {
    return 'no_indicado';
  }

  $labels = contact_request_type_labels();
  return array_key_exists($value, $labels) ? $value : 'no_indicado';
}

function contact_request_type_label(string $value): string {
  $labels = contact_request_type_labels();
  $value = contact_normalize_request_type($value);
  return $labels[$value] ?? $value;
}

function send_contact(array $data): bool {
  $name = $data['name'] ?? '';
  $phone = $data['phone'] ?? '';
  $email = $data['email'] ?? '';
  $requestType = contact_request_type_label((string) ($data['tipo_solicitud'] ?? ''));
  $service = $data['service'] ?? '';
  $message = $data['message'] ?? '';

  $subject = '[Web] ' . ($requestType !== '' ? $requestType : 'Nueva solicitud de presupuesto');
  $body = "Tipo de solicitud: {$requestType}\nNombre: {$name}\nTeléfono: {$phone}\nEmail: {$email}\nServicio: {$service}\n\nMensaje:\n{$message}";

  $smtp = config('smtp', []);
  $brand = config('brand', []);
  $recipients = contact_recipients($brand);
  if (count($recipients) === 0) {
    error_log('Mailer error: no valid contact recipients configured');
    return false;
  }

  if (!empty($smtp['host']) && !empty($smtp['user']) && !empty($smtp['pass'])) {
    try {
      if (file_exists(__DIR__ . '/../vendor/autoload.php')) { require_once __DIR__ . '/../vendor/autoload.php'; }
      if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return mail(implode(',', $recipients), $subject, $body, "From: {$smtp['from']}");
      }
      $mail = new PHPMailer\PHPMailer\PHPMailer(true);
      $mail->isSMTP();
      $mail->Host = $smtp['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $smtp['user'];
      $mail->Password = $smtp['pass'];
      $mail->SMTPSecure = $smtp['secure'];
      $mail->Port = $smtp['port'];
      $mail->CharSet = 'UTF-8';
      $mail->setFrom($smtp['from'], $smtp['from_name']);
      foreach ($recipients as $recipient) {
        $mail->addAddress($recipient);
      }
      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->send();
      return true;
    } catch (Throwable $e) {
      error_log('Mailer error: '.$e->getMessage());
      return false;
    }
  }
  $headers = "From: {$smtp['from']}\r\nContent-Type: text/plain; charset=UTF-8";
  return mail(implode(',', $recipients), $subject, $body, $headers);
}
