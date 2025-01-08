<?php
require_once 'config/database.php';

try {
    // Kiểm tra cột email
    $check_email = "SHOW COLUMNS FROM users LIKE 'email'";
    $result_email = $conn->query($check_email);
    
    if ($result_email->rowCount() == 0) {
        // Thêm cột email nếu chưa tồn tại
        $sql_email = "ALTER TABLE users ADD COLUMN email VARCHAR(255) NULL";
        $conn->exec($sql_email);
        echo "Đã thêm cột email thành công!<br>";
    }

    // Kiểm tra cột reset_token
    $check_token = "SHOW COLUMNS FROM users LIKE 'reset_token'";
    $result_token = $conn->query($check_token);
    
    if ($result_token->rowCount() == 0) {
        // Thêm các cột reset password nếu chưa tồn tại
        $sql_token = "ALTER TABLE users 
                ADD COLUMN reset_token VARCHAR(64) NULL,
                ADD COLUMN reset_token_expiry DATETIME NULL";
        
        $conn->exec($sql_token);
        echo "Đã thêm cột reset_token và reset_token_expiry thành công!";
    }

    echo "<br>Cập nhật database hoàn tất!";
    
} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}

// Hiển thị cấu trúc bảng để kiểm tra
try {
    echo "<br><br>Cấu trúc bảng users hiện tại:<br>";
    $show_columns = "SHOW COLUMNS FROM users";
    $columns = $conn->query($show_columns);
    foreach($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . "<br>";
    }
} catch(PDOException $e) {
    echo "Lỗi khi hiển thị cấu trúc bảng: " . $e->getMessage();
}
?> 