<?php
session_start();
require_once 'config/database.php';
// Bật hiển thị lỗi chi tiết hơn
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Kiểm tra kết nối database
try {
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   error_log("Kết nối database thành công");
} catch(PDOException $e) {
   error_log("Lỗi kết nối database: " . $e->getMessage());
   echo json_encode([
       'success' => false,
       'message' => 'Lỗi kết nối cơ sở dữ liệu'
   ]);
   exit;
}

// Debug: In ra thông tin POST
error_log("POST data: " . print_r($_POST, true));
error_log("Session data: " . print_r($_SESSION, true));
try {
   // Lấy dữ liệu từ form
   error_log("Bắt đầu xử lý đánh giá...");
   error_log("User ID: " . $_SESSION['user_id']);
   error_log("Rating: " . $_POST['rating']);
   error_log("Nội dung: " . $_POST['noiDung']);
   
   $soSao = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
   $noiDung = isset($_POST['noiDung']) ? trim($_POST['noiDung']) : '';
   $userId = $_SESSION['user_id'];
        // Validate dữ liệu
   if ($soSao < 1 || $soSao > 5) {
       throw new Exception('Số sao không hợp lệ');
   }
    if (empty($noiDung)) {
       throw new Exception('Vui lòng nhập nội dung đánh giá');
   }
    // SQL để lưu đánh giá
   $sql = "INSERT INTO danh_gia (so_sao, noi_dung, user_id, ngay_tao) 
           VALUES (:so_sao, :noi_dung, :user_id, NOW())";
   
   error_log("SQL Query: " . $sql);
   
   $stmt = $conn->prepare($sql);
   
   $params = [
       ':so_sao' => $soSao,
       ':noi_dung' => $noiDung,
       ':user_id' => $userId
   ];
   
   error_log("Parameters: " . print_r($params, true));
   
   if ($stmt->execute($params)) {
       echo json_encode([
           'success' => true,
           'message' => 'Cảm ơn bạn đã đánh giá!'
       ]);
   } else {
       error_log("Database error: " . print_r($stmt->errorInfo(), true));
       throw new Exception('Lỗi khi thực thi câu lệnh SQL');
   }
    } catch (Exception $e) {
   error_log("Error: " . $e->getMessage());
   echo json_encode([
       'success' => false,
       'message' => $e->getMessage()
   ]);
} catch (PDOException $e) {
   error_log("PDO Error: " . $e->getMessage());
   echo json_encode([
       'success' => false,
       'message' => 'Có lỗi xảy ra khi lưu đánh giá vào database'
   ]);
}
?>