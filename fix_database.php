<?php
require_once 'config/database.php';

try {
    // 1. Kiểm tra xem bảng users có tồn tại không
    $tableExists = $conn->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    
    if ($tableExists) {
        echo "Bảng users đã tồn tại.<br>";
        
        // 2. Kiểm tra và thêm cột reset_token nếu chưa có
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
        if ($result->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) NULL");
            echo "Đã thêm cột reset_token.<br>";
        } else {
            echo "Cột reset_token đã tồn tại.<br>";
        }
        
        // 3. Kiểm tra và thêm cột reset_token_expiry nếu chưa có
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token_expiry'");
        if ($result->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN reset_token_expiry DATETIME NULL");
            echo "Đã thêm cột reset_token_expiry.<br>";
        } else {
            echo "Cột reset_token_expiry đã tồn tại.<br>";
        }
        
        // 4. Kiểm tra và thêm cột email nếu chưa có
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
        if ($result->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255) NULL");
            echo "Đã thêm cột email.<br>";
        } else {
            echo "Cột email đã tồn tại.<br>";
        }
    } else {
        // Tạo bảng mới nếu chưa tồn tại
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) NULL,
            reset_token VARCHAR(64) NULL,
            reset_token_expiry DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql);
        echo "Đã tạo bảng users mới.<br>";
    }
    
    // Hiển thị cấu trúc bảng hiện tại
    echo "<br>Cấu trúc bảng users hiện tại:<br>";
    $columns = $conn->query("SHOW COLUMNS FROM users");
    while($column = $columns->fetch()) {
        echo $column['Field'] . " - " . $column['Type'] . "<br>";
    }
    
} catch(PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
?> 