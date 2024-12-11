<?php
header('Content-Type: text/html; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/PHPMailer.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar campos
    $nombre = htmlspecialchars($_POST['nombre'] ?? '');
    $correo = htmlspecialchars($_POST['correo'] ?? '');
    $telefono = htmlspecialchars($_POST['telefono'] ?? '');
    $asunto = htmlspecialchars($_POST['asunto'] ?? '');
    $mensaje = htmlspecialchars($_POST['mensaje'] ?? '');
    $privacidad = isset($_POST['privacidad']) ? 'Aceptada' : 'No aceptada';

    if (empty($nombre) || empty($correo) || empty($telefono) || empty($asunto) || empty($mensaje)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Cambiar a DEBUG_OFF en producción
        $mail->isSMTP();
        $mail->Host = 'mail.edgecloud.com.mx'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'contacto@edgecloud.com.mx'; 
        $mail->Password = 'Operaciones1'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Configuración del remitente y destinatario
        $mail->setFrom('contacto@edgecloud.com.mx', 'ITBSA WEB');
        $mail->addAddress('contacto@edgecloud.com.mx'); // Aquí va la dirección a la que se enviará el correo

        // Codificación en UTF-8
        $mail->CharSet = 'UTF-8';

        // Asunto y cuerpo del correo a ti
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = "
            <p><strong>Nombre:</strong> $nombre</p>
            <p><strong>Correo:</strong> $correo</p>
            <p><strong>Teléfono:</strong> $telefono</p>
            <p><strong>Mensaje:</strong></p>
            <p>$mensaje</p>
        ";

        // Enviar correo a ti
        $mail->send();

        // Ahora enviamos el correo de confirmación al usuario
        $mail->clearAddresses(); // Limpiar la dirección a la que se envía el primer correo
        $mail->addAddress($correo); // Añadir el correo del usuario como destinatario

        // Asunto y cuerpo del correo de confirmación
        $mail->Subject = "¡Gracias por ponerte en contacto con nosotros!";
        $mail->Body = "
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f9;
                        margin: 0;
                        padding: 0;
                    }
                    .email-container {
                        background-color: #ffffff;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        max-width: 600px;
                        margin: 20px auto;
                    }
                    .header {
                        background-color: #0044cc;
                        color: #ffffff;
                        padding: 15px;
                        text-align: center;
                        border-radius: 8px 8px 0 0;
                    }
                    .header h1 {
                        margin: 0;
                    }
                    .content {
                        padding: 20px;
                        font-size: 16px;
                        color: #333333;
                    }
                    .social-links {
                        margin-top: 20px;
                        text-align: center;
                    }
                    .social-links a {
                        display: inline-block;
                        margin: 10px;
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        background-color: #0044cc;
                        color: white;
                        text-align: center;
                        line-height: 40px;
                        font-size: 18px;
                        text-decoration: none;
                    }
                    .social-links a:hover {
                        background-color: #0033aa;
                    }
                    .footer {
                        text-align: center;
                        font-size: 12px;
                        color: #888888;
                        padding: 10px 0;
                    }
                </style>
            </head>
           <body>
                <div class='email-container'>
                    <div class='header'>
                        <h1>¡Gracias por tu mensaje, $nombre!</h1>
                    </div>
                    <div class='content'>
                        <p>¡Gracias por ponerte en contacto con nosotros! Hemos recibido tu mensaje y nos pondremos en contacto contigo lo más pronto posible.</p>
                        <p>Te agradecemos por confiar en ITBSA. ¡Te responderemos en breve!</p>
                        <a href='https://www.itbsa.com.mx' class='btn'>Visita nuestro sitio web</a>
                    </div>
                    <div class='footer'>
                        <p>&copy; 2024 ITBSA. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        // Enviar correo de confirmación al usuario
        $mail->send();

        // Redirigir a la página de éxito
        header('Location: ./contact.html?success=1');
        exit;
    } catch (Exception $e) {
        echo "El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}";
    }
}
