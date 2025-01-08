<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID món ăn']);
    exit;
}

try {
    $id = (int)$_GET['id'];
    
    $sql = "SELECT mon_an.*, danh_muc.ten as ten_danh_muc 
            FROM mon_an 
            JOIN danh_muc ON mon_an.danh_muc_id = danh_muc.id 
            WHERE mon_an.id = :id";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $monAn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($monAn) {
        echo json_encode([
            'success' => true,
            'ten' => $monAn['ten'],
            'ten_danh_muc' => $monAn['ten_danh_muc'],
            'mo_ta' => $monAn['mo_ta'],
            'chi_tiet' => $monAn['chi_tiet']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy món ăn']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Lỗi khi truy vấn dữ liệu']);
}
?> 