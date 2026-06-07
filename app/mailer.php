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

function send_contact(array $data): bool {
  $name = $data['name'] ?? '';
  $phone = $data['phone'] ?? '';
  $email = $data['email'] ?? '';
  $service = $data['service'] ?? '';
  $message = $data['message'] ?? '';

  $subject = '[Web] Nueva solicitud de presupuesto';
  $body = "Nombre: {$name}\nTeléfono: {$phone}\nEmail: {$email}\nServicio: {$service}\n\nMensaje:\n{$message}";

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
