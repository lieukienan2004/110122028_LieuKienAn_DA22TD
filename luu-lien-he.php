<?php
session_start();
require_once 'config/database.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để gửi tin nhắn']);
    exit;
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Lấy dữ liệu từ form
$hoTen = $_POST['hoTen'] ?? '';
$email = $_POST['email'] ?? '';
$soDienThoai = $_POST['soDienThoai'] ?? '';
$noiDung = $_POST['noiDung'] ?? '';

// Validate dữ liệu
if (empty($hoTen) || empty($email) || empty($noiDung)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
    exit;
}

try {
    // Lưu vào database
    $sql = "INSERT INTO lien_he (ho_ten, email, so_dien_thoai, noi_dung) VALUES (:ho_ten, :email, :so_dien_thoai, :noi_dung)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ho_ten', $hoTen);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':so_dien_thoai', $soDienThoai);
    $stmt->bindParam(':noi_dung', $noiDung);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Gửi tin nhắn thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi gửi tin nhắn']);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại sau']);
} 