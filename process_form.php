<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

  // Verify reCAPTCHA response
  $recaptcha_response = $_POST['g-recaptcha-response'];
  $recaptcha_secret_key = 'YOUR_SECRET_KEY';
  $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
  $recaptcha_data = array(
    'secret' => $recaptcha_secret_key,
    'response' => $recaptcha_response
  );
  $recaptcha_options = array(
    'http' => array (
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($recaptcha_data)
    )
  );
  $recaptcha_context = stream_context_create($recaptcha_options);
  $recaptcha_result = json_decode(file_get_contents($recaptcha_url, false, $recaptcha_context), true);

  if ($recaptcha_result['success']) {
    // reCAPTCHA verification successful, send email
    $to = 'youremail@example.com';
    $subject = 'New Contact Form Submission';
    $body = "Name: $name\nEmail: $email\nMessage:\n$message";

    if (mail($to, $subject, $body)) {
      echo 'Thank you for your message. We will be in touch soon.';
    } else {
      echo 'An error occurred while sending your message. Please try again later.';
    }
  } else {
    // reCAPTCHA verification failed, display error message
    echo 'reCAPTCHA verification failed. Please try again.';
  }
}
?>
