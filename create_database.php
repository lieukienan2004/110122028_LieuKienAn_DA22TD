<?php
require_once 'config/database.php';

try {
    // Tắt ràng buộc khóa ngoại
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Xóa bảng nếu tồn tại
    $conn->exec("DROP TABLE IF EXISTS users");

    // Tạo bảng mới
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        reset_token VARCHAR(64) DEFAULT NULL,
        reset_token_expiry DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_username (username),
        UNIQUE KEY unique_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $conn->exec($sql);

    // Bật lại ràng buộc khóa ngoại
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Thêm dữ liệu mẫu
    $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hashed_password, 'admin@example.com']);

    echo "Đã tạo bảng users thành công!<br>";
    echo "Tài khoản mẫu đã được tạo:<br>";
    echo "Username: admin<br>";
    echo "Password: 123456<br>";
    echo "Email: admin@example.com";

} catch(PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
?> 