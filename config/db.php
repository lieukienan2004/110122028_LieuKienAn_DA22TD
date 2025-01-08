<?php
try {
    $host = 'localhost';
    $dbname = 'kienan';  // Tên database của bạn
    $username = 'root';   // Username mặc định của XAMPP
    $password = '';       // Password mặc định của XAMPP là rỗng

    // Tạo kết nối PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Thiết lập chế độ báo lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // Nếu kết nối thất bại, hiển thị lỗi
    echo "Lỗi kết nối: " . $e->getMessage();
    die();
}
?> 