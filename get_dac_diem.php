<?php
require_once 'config/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $sql = "SELECT mo_ta, gia, chi_tiet FROM mon_an WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Query result for ID $id: " . print_r($result, true));
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'dac_diem' => $result['mo_ta'],
                'gia' => $result['gia'],
                'chi_tiet' => $result['chi_tiet']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy thông tin món ăn'
            ]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi khi truy vấn dữ liệu: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin ID món ăn'
    ]);
}
?> 