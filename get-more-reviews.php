<?php
session_start();
require_once 'config/database.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;

try {
    $reviews = getDanhGiaWithPagination($conn, $page, $perPage);
    
    // Format dữ liệu trước khi trả về
    foreach ($reviews as &$review) {
        $review['ngay_tao'] = date('d/m/Y H:i', strtotime($review['ngay_tao']));
    }
    
    echo json_encode([
        'success' => true,
        'reviews' => $reviews
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi tải đánh giá'
    ]);
} 