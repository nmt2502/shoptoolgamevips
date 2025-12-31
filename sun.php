<?php
session_start();

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

/* API KEY */
$API_KEY = 'SUNWIN_PRIVATE_2025';
$clientKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($clientKey !== $API_KEY) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid API KEY']);
    exit;
}

/* API gốc */
$api_url = 'https://sunwinsaygex-tzz9.onrender.com/api/sun';

/* Gọi API */
$ch = curl_init($api_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

/* ❌ KHÔNG CẦN curl_close() NỮA */

if ($response === false) {
    http_response_code(503);
    echo json_encode([
        'error' => 'Không thể kết nối server dự đoán',
        'details' => $curl_error
    ]);
    exit;
}

if ($http_code !== 200) {
    http_response_code(502);
    echo json_encode([
        'error' => 'Server dự đoán lỗi',
        'status_code' => $http_code
    ]);
    exit;
}

/* Trả JSON nguyên gốc */
echo $response;
