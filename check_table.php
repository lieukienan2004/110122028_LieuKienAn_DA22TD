<?php
require_once 'config/database.php';

try {
    // Hiển thị cấu trúc bảng hiện tại
    echo "Cấu trúc bảng users:<br>";
    $show_columns = "SHOW COLUMNS FROM users";
    $result = $conn->query($show_columns);
    
    foreach($result as $row) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?> 