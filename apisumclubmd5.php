<?php
// Bắt đầu session ở đầu file
session_start();

header('Content-Type: application/json; charset=utf-8');

// --- Cấu hình ---
// Tên miền được phép truy cập file này
$allowed_domains = ['tooldudoanai.fun', 'www.tooldudoanai.fun']; 
// Endpoint API gốc để lấy dữ liệu
$api_url = 'https://sumclubmd5-hyba.onrender.com/'; 

// --- Logic ---

// 1. Kiểm tra tên miền truy cập (Referer)
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$domain = parse_url($referer, PHP_URL_HOST);

if (!in_array($domain, $allowed_domains)) {
    http_response_code(403); // Forbidden
    // Hiển thị thông báo khi có người cố tình truy cập trực tiếp hoặc từ domain khác
    echo json_encode(['error' => 'Có cái đầu buồi crack dc api']);
    exit();
}

/* LƯU Ý VỀ XÁC THỰC:
Mã nguồn HTML của bạn đang dùng sessionStorage (phía trình duyệt) để kiểm tra đăng nhập.
Mã nguồn PHP này có thể kiểm tra $_SESSION (phía máy chủ).
Hai cơ chế này không tự động liên kết với nhau. 
Nếu bạn có một hệ thống đăng nhập bằng PHP, hãy bỏ comment (dấu //) ở đoạn code dưới đây.
*/
// if (!isset($_SESSION['user_info']) || !$_SESSION['user_info']['isActive']) {
//     http_response_code(401); // Unauthorized
//     echo json_encode(['error' => 'Chưa xác thực hoặc key chưa kích hoạt.']);
//     exit();
// }

// 2. Gọi API gốc bằng cURL để lấy dữ liệu
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// 3. Xử lý và trả kết quả về cho file HTML
if ($response === false) {
    // Lỗi từ cURL (VD: timeout, không kết nối được)
    http_response_code(503); // Service Unavailable
    echo json_encode(['error' => 'Không thể kết nối đến server dự đoán.', 'details' => $curl_error]);
} elseif ($http_code !== 200) {
    // API gốc trả về lỗi (VD: 404, 500)
    http_response_code(502); // Bad Gateway
    echo json_encode(['error' => 'Server dự đoán đang gặp sự cố.', 'status_code' => $http_code]);
} else {
    // Thành công, chuyển tiếp nguyên vẹn dữ liệu JSON về cho client
    echo $response;
}
?>
