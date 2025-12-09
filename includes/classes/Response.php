<?php

/**
 * Clase estática para manejar respuestas HTTP y JSON.
 */
class Response {
    /**
     * Envía una respuesta JSON estandarizada y termina la ejecución.
     */
    public static function sendJson($success, $message, $data = [], $httpCode = 200) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}