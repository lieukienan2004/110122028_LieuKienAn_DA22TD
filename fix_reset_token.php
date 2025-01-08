<?php
try {
    // Kết nối trực tiếp với MySQL
    $conn = new PDO("mysql:host=localhost;dbname=kienan", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kiểm tra xem cột reset_token đã tồn tại chưa
    $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
    
    if ($checkColumn->rowCount() == 0) {
        // Thêm cột reset_token và reset_token_expiry
        $sql = "ALTER TABLE users 
                ADD COLUMN reset_token VARCHAR(64) NULL AFTER email,
                ADD COLUMN reset_token_expiry DATETIME NULL AFTER reset_token";
        $conn->exec($sql);
        echo "Đã thêm cột reset_token và reset_token_expiry thành công!<br>";
    } else {
        echo "Cột reset_token đã tồn tại.<br>";
    }

    // Hiển thị cấu trúc bảng hiện tại
    echo "<br>Cấu trúc bảng users hiện tại:<br>";
    $columns = $conn->query("SHOW COLUMNS FROM users");
    while($column = $columns->fetch()) {
        echo $column['Field'] . " - " . $column['Type'] . "<br>";
    }

    // Hiển thị dữ liệu trong bảng
    echo "<br>Dữ liệu trong bảng users:<br>";
    $users = $conn->query("SELECT * FROM users");
    while($user = $users->fetch()) {
        echo "ID: " . $user['id'] . " - Username: " . $user['username'] . " - Email: " . $user['email'] . "<br>";
    }

} catch(PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
?> 