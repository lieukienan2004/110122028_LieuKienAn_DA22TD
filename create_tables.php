<?php
// Kết nối trực tiếp không qua config
try {
    $conn = new PDO("mysql:host=localhost", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tạo database
    $conn->exec("CREATE DATABASE IF NOT EXISTS kienan");
    $conn->exec("USE kienan");

    // Xóa bảng cũ nếu tồn tại
    $conn->exec("DROP TABLE IF EXISTS users");

    // Tạo bảng users mới
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        reset_token VARCHAR(64) NULL,
        reset_token_expiry DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql);

    // Thêm dữ liệu mẫu
    $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
    $insert_sql = "INSERT INTO users (username, password, email) VALUES 
        ('admin', :password, 'admin@gmail.com')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->execute(['password' => $hashed_password]);

    echo "Đã tạo CSDL và bảng thành công!<br>";
    echo "Tài khoản mẫu:<br>";
    echo "Username: admin<br>";
    echo "Password: 123456<br>";
    echo "Email: admin@gmail.com";

} catch(PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
?> 