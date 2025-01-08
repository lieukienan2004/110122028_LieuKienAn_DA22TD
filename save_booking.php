<?php
header('Content-Type: application/json');

// Kết nối database
$conn = new mysqli("localhost", "root", "", "kienan");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Lỗi kết nối database']));
}
$conn->set_charset("utf8mb4");

// Nhận dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra dữ liệu
if (!$data['name'] || !$data['phone'] || !$data['hotel']) {
    die(json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']));
}

try {
    // Chuẩn bị câu lệnh SQL
    $sql = "INSERT INTO bookings (
        customer_name,
        phone_number,
        hotel_name,
        check_in_date,
        check_out_date,
        room_count,
        adult_count,
        children_count,
        infant_count,
        promo_code,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    // Chuẩn bị và thực thi câu lệnh
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssiiiis",
        $data['name'],
        $data['phone'],
        $data['hotel'],
        $data['checkIn'],
        $data['checkOut'],
        $data['rooms'],
        $data['adults'],
        $data['children'],
        $data['infants'],
        $data['promoCode']
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Đặt phòng thành công',
            'booking_id' => $conn->insert_id
        ]);
    } else {
        throw new Exception('Không thể lưu đơn đặt phòng');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 