<?php
try {
    // Kết nối MySQL không cần chọn database
    $conn = new PDO("mysql:host=localhost", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tạo database nếu chưa tồn tại
    $conn->exec("CREATE DATABASE IF NOT EXISTS kienan");
    $conn->exec("USE kienan");
    
    // Tạo bảng users với đầy đủ các trường
    $sql = "CREATE TABLE IF NOT EXISTS users (
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
    
    // Thêm tài khoản mẫu nếu bảng trống
    $check = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($check == 0) {
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hashed_password, 'admin@example.com']);
    }
    
    echo "Đã thiết lập database và bảng thành công!<br>";
    echo "Cấu trúc bảng users hiện tại:<br>";
    
    $columns = $conn->query("SHOW COLUMNS FROM users");
    while($column = $columns->fetch()) {
        echo $column['Field'] . " - " . $column['Type'] . "<br>";
    }
    
    echo "<br>Tài khoản mẫu:<br>";
    echo "Username: admin<br>";
    echo "Password: 123456<br>";
    echo "Email: admin@example.com";
    
} catch(PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
?> 