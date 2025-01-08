<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Lấy và làm sạch dữ liệu
    $hoTen = filter_input(INPUT_POST, 'hoTen', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $soDienThoai = filter_input(INPUT_POST, 'soDienThoai', FILTER_SANITIZE_STRING);
    $noiDung = filter_input(INPUT_POST, 'noiDung', FILTER_SANITIZE_STRING);
    
    // Kiểm tra dữ liệu
    if (empty($hoTen) || empty($email) || empty($noiDung)) {
        $response['message'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Email không hợp lệ';
        echo json_encode($response);
        exit;
    }
    
    try {
        $sql = "INSERT INTO lien_he (ho_ten, email, so_dien_thoai, noi_dung) 
                VALUES (:ho_ten, :email, :so_dien_thoai, :noi_dung)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':ho_ten', $hoTen);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':so_dien_thoai', $soDienThoai);
        $stmt->bindParam(':noi_dung', $noiDung);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.';
        } else {
            $response['message'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $response['message'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
    }
    
    echo json_encode($response);
    exit;
}
?>