<?php
function send_contact(array $data): bool {
  global $config;
  $name = $data['name'] ?? '';
  $phone = $data['phone'] ?? '';
  $email = $data['email'] ?? '';
  $service = $data['service'] ?? '';
  $message = $data['message'] ?? '';

  $subject = '[Web] Nueva solicitud de presupuesto';
  $body = "Nombre: {$name}\nTeléfono: {$phone}\nEmail: {$email}\nServicio: {$service}\n\nMensaje:\n{$message}";

  if (!empty($config['smtp']['host']) && !empty($config['smtp']['user']) && !empty($config['smtp']['pass'])) {
    try {
      if (file_exists(__DIR__ . '/../vendor/autoload.php')) { require_once __DIR__ . '/../vendor/autoload.php'; }
      if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return mail($config['brand']['email'], $subject, $body, "From: {$config['smtp']['from']}");
      }
      $mail = new PHPMailer\PHPMailer\PHPMailer(true);
      $mail->isSMTP();
      $mail->Host = $config['smtp']['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $config['smtp']['user'];
      $mail->Password = $config['smtp']['pass'];
      $mail->SMTPSecure = $config['smtp']['secure'];
      $mail->Port = $config['smtp']['port'];
      $mail->CharSet = 'UTF-8';
      $mail->setFrom($config['smtp']['from'], $config['smtp']['from_name']);
      $mail->addAddress($config['brand']['email'], $config['brand']['name']);
      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->send();
      return true;
    } catch (Throwable $e) {
      error_log('Mailer error: '.$e->getMessage());
      return false;
    }
  }
  $headers = "From: {$config['smtp']['from']}\r\nContent-Type: text/plain; charset=UTF-8";
  return mail($config['brand']['email'], $subject, $body, $headers);
}
