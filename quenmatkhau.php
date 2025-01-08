<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $email = trim($_POST['email']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        // Kiểm tra email có tồn tại
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 1) {
            if ($new_password === $confirm_password) {
                // Mã hóa mật khẩu mới
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Cập nhật mật khẩu mới
                $update_sql = "UPDATE users SET password = ? WHERE email = ?";
                $update_stmt = $conn->prepare($update_sql);
                if ($update_stmt->execute([$hashed_password, $email])) {
                    $success = "Mật khẩu đã được cập nhật thành công!";
                } else {
                    $error = "Có lỗi xảy ra, vui lòng thử lại!";
                }
            } else {
                $error = "Mật khẩu xác nhận không khớp!";
            }
        } else {
            $error = "Email không tồn tại trong hệ thống!";
        }
    } catch(PDOException $e) {
        $error = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Đặt Lại Mật Khẩu</h2>
        <?php if(isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Nhập email của bạn">
            </div>
            
            <div class="form-group">
                <label>Mật khẩu mới</label>
                <input type="password" name="new_password" required placeholder="Nhập mật khẩu mới">
            </div>
            
            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required placeholder="Nhập lại mật khẩu mới">
            </div>
            
            <button type="submit" class="btn-submit">Đặt lại mật khẩu</button>
        </form>
        <div class="form-footer">
            <p><a href="dangnhap.php">Quay lại đăng nhập</a></p>
        </div>
    </div>
</body>
</html> 