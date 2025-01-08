<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config/database.php';

try {
    // Kiểm tra kết nối database
    if (!$conn) {
        throw new Exception("Không thể kết nối đến database");
    }

    $sql = "SELECT danh_gia.*, users.username, users.avatar 
            FROM danh_gia 
            JOIN users ON danh_gia.user_id = users.id 
            ORDER BY danh_gia.ngay_tao DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Xử lý dữ liệu trước khi trả về
    foreach ($reviews as &$review) {
        // Đảm bảo avatar có giá trị mặc định nếu null
        $review['avatar'] = $review['avatar'] ?? 'default-avatar.jpg';
        
        // Làm sạch dữ liệu
        $review['username'] = htmlspecialchars($review['username']);
        $review['noi_dung'] = htmlspecialchars($review['noi_dung']);
        
        // Định dạng ngày tháng
        $review['ngay_tao'] = date('Y-m-d H:i:s', strtotime($review['ngay_tao']));
    }
    
    echo json_encode([
        'success' => true,
        'reviews' => $reviews
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi tải đánh giá: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?> 