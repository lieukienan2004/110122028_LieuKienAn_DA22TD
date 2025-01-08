<?php
session_start();
require_once 'config/database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Kiểm tra token có hợp lệ và chưa hết hạn
    $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() == 0) {
        die("Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn!");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $token = $_POST['token'];
    
    if ($new_password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Cập nhật mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([$hashed_password, $token]);
        
        $success = "Mật khẩu đã được đặt lại thành công!";
        header("refresh:3;url=dangnhap.php");
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
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            
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
    </div>
</body>
</html> 