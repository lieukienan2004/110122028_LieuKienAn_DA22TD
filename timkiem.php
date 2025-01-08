<?php
// Sửa lại thông tin kết nối database
$conn = new mysqli("localhost", "root", "", "kienan");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Thêm function để lưu booking
function saveBooking($data) {
    global $conn;
    
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
        promo_code
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
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
    
    return $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm khách sạn</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .search-container {
            width: 1000px;
            margin: 0 auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        h2 {
            font-size: 32px;
            margin-bottom: 30px;
            text-align: center;
            color: #333;
        }

        .nav-tabs {
            display: flex;
            border-bottom: 2px solid #eee;
            margin-bottom: 30px;
        }

        .nav-item {
            padding: 15px 30px;
            cursor: pointer;
            position: relative;
            color: #666;
            font-size: 18px;
        }

        .nav-item.active {
            color: #f39c12;
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #f39c12;
        }

        .search-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-group {
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-group i {
            color: #666;
            font-size: 24px;
        }

        .input-group input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 16px;
        }

        .input-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 4px;
        }

        .input-value {
            font-size: 18px;
            color: #333;
            margin-top: 8px;
        }

        .search-btn {
            width: 100%;
            padding: 15px;
            background: #f39c12;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.3s;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .search-btn:hover {
            background: #e67e22;
        }

        .search-btn i {
            font-size: 18px;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        /* Thêm icon font */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

        .dropdown-container {
            position: relative;
            width: 100%;
        }

        .selected-value {
            padding: 8px 0;
            cursor: pointer;
            font-size: 18px;
            color: #333;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
        }

        .dropdown-menu.show {
            display: block;
        }

        .location-group {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .location-group:last-child {
            border-bottom: none;
        }

        .location-city {
            padding: 5px 15px;
            font-weight: bold;
            color: #666;
            background: #f5f5f5;
        }

        .location-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .location-item:hover {
            background: #f0f0f0;
        }

        .calendar-container {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
        }

        .calendar {
            padding: 20px;
            min-width: 300px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header button {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 5px 10px;
        }

        .calendar-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .weekdays div {
            padding: 5px;
            color: #666;
        }

        .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .days div {
            padding: 8px;
            text-align: center;
            cursor: pointer;
            border-radius: 4px;
        }

        .days div:hover:not(.empty):not(.selected) {
            background-color: #f0f0f0;
        }

        .days .empty {
            color: #ccc;
            cursor: default;
        }

        .days .selected {
            background-color: #f39c12;
            color: white;
        }

        .days .in-range {
            background-color: #fdebd0;
        }

        .date-picker {
            cursor: pointer;
        }

        .calendar-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .calendar-popup.show {
            display: block;
        }

        /* Thêm overlay khi calendar hiện lên */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .overlay.show {
            display: block;
        }

        .room-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            z-index: 1000;
            min-width: 300px;
        }

        .room-popup.show {
            display: block;
        }

        .room-selector {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .room-count {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .selector-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .selector-controls button {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .selector-controls button:disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .room-detail {
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .room-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .age-note {
            color: #666;
            font-size: 14px;
            font-style: italic;
        }

        .guest-selector {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .control-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ddd;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #666;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background-color: #f5f5f5;
        }

        .control-btn:disabled {
            color: #ccc;
            border-color: #eee;
            cursor: not-allowed;
        }

        .selector-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .selector-controls span {
            min-width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .guest-type {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            color: #666;
        }

        .guest-info {
            opacity: 0.7;
        }

        .room-detail {
            padding: 20px;
            background: white;
            border-radius: 8px;
        }

        .room-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .room-count {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .control-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ddd;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #666;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background-color: #f5f5f5;
        }

        .control-btn:disabled {
            color: #ccc;
            border-color: #eee;
            cursor: not-allowed;
        }

        .selector-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .selector-controls span {
            min-width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .guest-type {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            color: #666;
        }

        .guest-info {
            opacity: 0.7;
        }

        .age-note {
            margin-top: 15px;
            font-size: 14px;
            color: #666;
            font-style: italic;
        }

        .text-input {
            width: 100%;
            padding: 8px 0;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
        }

        .text-input::placeholder {
            color: #999;
        }

        /* Ẩn mũi tên trên input số */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .bookings-list {
            margin-top: 40px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .bookings-list h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f39c12;
        }

        .booking-item {
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }

        .booking-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .hotel-name {
            font-weight: bold;
            color: #f39c12;
        }

        .booking-date {
            color: #666;
            font-size: 14px;
        }

        .booking-details {
            color: #555;
        }

        .booking-details p {
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .booking-details i {
            color: #f39c12;
            width: 20px;
        }

        .no-bookings {
            text-align: center;
            color: #666;
            padding: 20px;
        }

        .booking-success {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 50px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 1000;
            text-align: center;
        }

        .success-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .success-content i {
            font-size: 50px;
            color: #4CAF50;
        }

        .success-content h3 {
            color: #333;
            margin: 0;
        }

        .recent-bookings {
            margin-top: 40px;
            padding: 25px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .bookings-header {
            margin-bottom: 25px;
        }

        .bookings-header h3 {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bookings-header h3 i {
            color: #f39c12;
            font-size: 24px;
        }

        .header-line {
            margin-top: 15px;
            height: 3px;
            background: linear-gradient(to right, #f39c12, #f1c40f);
            border-radius: 3px;
        }

        .booking-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #eee;
            transition: all 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .hotel-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hotel-name {
            font-size: 18px;
            font-weight: 600;
            color: #f39c12;
        }

        .booking-time {
            color: #95a5a6;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .booking-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .detail-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #34495e;
        }

        .detail-item i {
            color: #f39c12;
            width: 20px;
            text-align: center;
        }

        .date-range {
            background: #fff;
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .guests {
            background: #fff;
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid #eee;
            flex: 1;
            min-width: 120px;
            justify-content: center;
        }

        .no-bookings {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .no-bookings i {
            font-size: 40px;
            color: #bdc3c7;
        }

        @media screen and (max-width: 768px) {
            .booking-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .guests {
                width: 100%;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="search-container">
        <div class="close-btn">&times;</div>
        
        <h2>Tìm kiếm khách sạn</h2>

        <div class="nav-tabs">
            <div class="nav-item active">Khách sạn</div>
        </div>

        <form id="searchForm" onsubmit="return handleSearch(event)">
            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <div class="input-content dropdown-container">
                    <div class="input-label">Chọn điểm đến, khách sạn theo sở thích ...</div>
                    <div class="selected-value" id="selectedLocation">Chọn địa điểm</div>
                    <div class="dropdown-menu" id="locationDropdown">
                        <div class="location-group">
                            <div class="location-city">Thành phố Trà Vinh</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Cửu Long')">Khách sạn Cửu Long</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Hoàng Gia')">Khách sạn Hoàng Gia</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Trà Vinh')">Khách sạn Trà Vinh</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Phương Nam')">Khách sạn Phương Nam</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Mỹ Trà')">Khách sạn Mỹ Trà</div>
                        </div>
                        <div class="location-group">
                            <div class="location-city">Huyện Càng Long</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Hương Trà')">Khách sạn Hương Trà</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Thanh Bình')">Khách sạn Thanh Bình</div>
                        </div>
                        <div class="location-group">
                            <div class="location-city">Huyện Cầu Kè</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Thiên Ân')">Khách sạn Thiên Ân</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Hoàng Long')">Khách sạn Hoàng Long</div>
                        </div>
                        <div class="location-group">
                            <div class="location-city">Huyện Tiểu Cần</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Tiểu Cần')">Khách sạn Tiểu Cần</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Tân Phú')">Khách sạn Tân Phú</div>
                        </div>
                        <div class="location-group">
                            <div class="location-city">Huyện Châu Thành</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Hòa Bình')">Khách sạn Hòa Bình</div>
                            <div class="location-item" onclick="selectLocation('Khách sạn Minh Châu')">Khách sạn Minh Châu</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group date-picker">
                <i class="far fa-calendar"></i>
                <div class="input-content">
                    <div class="input-label">Ngày nhận - Trả phòng</div>
                    <div class="selected-dates" id="selectedDates">Chọn ngày</div>
                </div>
            </div>

            <div class="input-group room-picker">
                <i class="fas fa-user"></i>
                <div class="input-content">
                    <div class="input-label">Số phòng - Số người</div>
                    <div class="selected-rooms" id="selectedRooms">1 Phòng - 1 Người lớn - 0 Trẻ em - 0 Em bé</div>
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-user"></i>
                <div class="input-content">
                    <div class="input-label">Họ và tên</div>
                    <input type="text" id="customerName" class="text-input" placeholder="Nhập họ và tên của bạn" required>
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-phone"></i>
                <div class="input-content">
                    <div class="input-label">Số điện thoại</div>
                    <input type="tel" id="phoneNumber" class="text-input" placeholder="Nhập số điện thoại của bạn" required pattern="[0-9]{10}" maxlength="10">
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-tag"></i>
                <div class="input-content">
                    <div class="input-label">Mã ưu đãi</div>
                    <input type="text" placeholder="Nhập mã ưu đãi">
                </div>
            </div>

            <button type="submit" class="search-btn">
                <i class="fas fa-hotel"></i>
                Đặt khách sạn
            </button>
        </form>
    </div>

    <div class="calendar-popup" id="calendarPopup">
        <div class="calendar">
            <div class="calendar-header">
                <button class="prev-month">&lt;</button>
                <h3 id="currentMonth">Dec 2024</h3>
                <button class="next-month">&gt;</button>
            </div>
            <div class="weekdays">
                <div>Su</div>
                <div>Mo</div>
                <div>Tu</div>
                <div>We</div>
                <div>Th</div>
                <div>Fr</div>
                <div>Sa</div>
            </div>
            <div class="days" id="calendarDays"></div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="room-popup" id="roomPopup">
        <div class="room-selector">
            <div class="room-detail">
                <div class="room-count">
                    <div class="selector-label">Số phòng</div>
                    <div class="selector-controls">
                        <button class="control-btn minus" id="minusRoom">−</button>
                        <span id="roomCount">1</span>
                        <button class="control-btn plus" id="plusRoom">+</button>
                    </div>
                </div>

                <div class="guest-info">
                    <div class="guest-type">
                        <div>Người lớn</div>
                        <div class="selector-controls">
                            <button class="control-btn minus" id="minusAdult">−</button>
                            <span id="adultCount">1</span>
                            <button class="control-btn plus" id="plusAdult">+</button>
                        </div>
                    </div>

                    <div class="guest-type">
                        <div>Trẻ em</div>
                        <div class="selector-controls">
                            <button class="control-btn minus" id="minusChild">−</button>
                            <span id="childCount">0</span>
                            <button class="control-btn plus" id="plusChild">+</button>
                        </div>
                    </div>

                    <div class="guest-type">
                        <div>Em bé</div>
                        <div class="selector-controls">
                            <button class="control-btn minus" id="minusInfant">−</button>
                            <span id="infantCount">0</span>
                            <button class="control-btn plus" id="plusInfant">+</button>
                        </div>
                    </div>
                </div>
                
                <div class="age-note">*Em bé: Dưới 4 tuổi/ Trẻ em: Từ 4 - dưới 12 tuổi</div>
            </div>
        </div>
    </div>

    <div id="successMessage" class="success-message" style="display: none;">
        <i class="fas fa-check-circle"></i>
        <span>Đặt phòng thành công!</span>
    </div>

    <div id="bookingSuccess" class="booking-success" style="display: none;">
        <div class="success-content">
            <i class="fas fa-check-circle"></i>
            <h3>Đặt phòng thành công!</h3>
        </div>
    </div>

    <div class="recent-bookings">
        <div class="bookings-header">
            <h3>
                <i class="fas fa-history"></i>
                Danh sách đã đặt gần đây
            </h3>
            <div class="header-line"></div>
        </div>
        
        <div class="bookings-container">
            <?php
            $conn = new mysqli("localhost", "root", "", "kienan");
            $conn->set_charset("utf8mb4");

            $sql = "SELECT * FROM bookings ORDER BY booking_date DESC LIMIT 5";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="booking-card">
                        <div class="booking-header">
                            <div class="hotel-info">
                                <i class="fas fa-hotel"></i>
                                <span class="hotel-name">' . htmlspecialchars($row["hotel_name"]) . '</span>
                            </div>
                            <div class="booking-time">
                                <i class="far fa-clock"></i>
                                ' . date('H:i d/m/Y', strtotime($row["booking_date"])) . '
                            </div>
                        </div>
                        
                        <div class="booking-details">
                            <div class="detail-row">
                                <div class="detail-item">
                                    <i class="fas fa-user"></i>
                                    <span>' . htmlspecialchars($row["customer_name"]) . '</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-phone"></i>
                                    <span>' . htmlspecialchars($row["phone_number"]) . '</span>
                                </div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-item date-range">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>' . date('d/m/Y', strtotime($row["check_in_date"])) . ' - ' . date('d/m/Y', strtotime($row["check_out_date"])) . '</span>
                                </div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-item guests">
                                    <i class="fas fa-bed"></i>
                                    <span>' . $row["room_count"] . ' Phòng</span>
                                </div>
                                <div class="detail-item guests">
                                    <i class="fas fa-user-friends"></i>
                                    <span>' . $row["adult_count"] . ' Người lớn</span>
                                </div>
                                <div class="detail-item guests">
                                    <i class="fas fa-child"></i>
                                    <span>' . $row["children_count"] . ' Trẻ em</span>
                                </div>
                                <div class="detail-item guests">
                                    <i class="fas fa-baby"></i>
                                    <span>' . $row["infant_count"] . ' Em bé</span>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="no-bookings">
                        <i class="fas fa-calendar-times"></i>
                        <p>Chưa có đơn đặt phòng nào</p>
                    </div>';
            }
            $conn->close();
            ?>
        </div>
    </div>

    <script>
        // Xử lý sự kiện click cho tabs
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.nav-item').forEach(tab => {
                    tab.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Xử lý nút đóng
        document.querySelector('.close-btn').addEventListener('click', function() {
            // Thêm code xử lý đóng form ở đây
            console.log('Đóng form tìm kiếm');
        });

        document.querySelector('.dropdown-container').addEventListener('click', function(e) {
            const dropdown = document.getElementById('locationDropdown');
            dropdown.classList.toggle('show');
            e.stopPropagation();
        });

        function selectLocation(location) {
            document.getElementById('selectedLocation').textContent = location;
            document.getElementById('locationDropdown').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
        }

        // Đóng dropdown khi click bên ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-container')) {
                document.getElementById('locationDropdown').classList.remove('show');
            }
        });

        // Ngăn dropdown đóng khi click vào các item
        document.getElementById('locationDropdown').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        let currentDate = new Date();
        let selectedStartDate = null;
        let selectedEndDate = null;

        function showCalendar() {
            document.getElementById('calendarPopup').classList.add('show');
            document.getElementById('overlay').classList.add('show');
            renderCalendar();
        }

        function hideCalendar() {
            document.getElementById('calendarPopup').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            
            document.getElementById('currentMonth').textContent = 
                new Intl.DateTimeFormat('en-US', { month: 'short', year: 'numeric' }).format(currentDate);
            
            const daysContainer = document.getElementById('calendarDays');
            daysContainer.innerHTML = '';
            
            // Add empty cells for days before first day of month
            for(let i = 0; i < firstDay.getDay(); i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'empty';
                emptyDay.textContent = '';
                daysContainer.appendChild(emptyDay);
            }
            
            // Add days of month
            for(let day = 1; day <= lastDay.getDate(); day++) {
                const dayElement = document.createElement('div');
                const date = new Date(year, month, day);
                
                dayElement.textContent = day;
                
                if (isDateSelected(date)) {
                    dayElement.className = 'selected';
                } else if (isDateInRange(date)) {
                    dayElement.className = 'in-range';
                }
                
                dayElement.addEventListener('click', () => selectDate(date));
                daysContainer.appendChild(dayElement);
            }
        }

        function selectDate(date) {
            if (!selectedStartDate || (selectedStartDate && selectedEndDate)) {
                selectedStartDate = date;
                selectedEndDate = null;
            } else {
                if (date < selectedStartDate) {
                    selectedEndDate = selectedStartDate;
                    selectedStartDate = date;
                } else {
                    selectedEndDate = date;
                }
            }
            
            updateDateDisplay();
            renderCalendar();
        }

        function isDateSelected(date) {
            return (selectedStartDate && date.getTime() === selectedStartDate.getTime()) ||
                   (selectedEndDate && date.getTime() === selectedEndDate.getTime());
        }

        function isDateInRange(date) {
            return selectedStartDate && selectedEndDate &&
                   date > selectedStartDate && date < selectedEndDate;
        }

        function updateDateDisplay() {
            const formatDate = (date) => {
                return date ? date.toLocaleDateString('vi-VN') : '';
            };
            
            const displayText = selectedStartDate ? 
                (selectedEndDate ? 
                    `${formatDate(selectedStartDate)} - ${formatDate(selectedEndDate)}` : 
                    formatDate(selectedStartDate)
                ) : 'Chọn ngày';
            
            document.getElementById('selectedDates').textContent = displayText;
        }

        // Event Listeners
        document.querySelector('.prev-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.querySelector('.next-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        document.querySelector('.date-picker').addEventListener('click', (e) => {
            showCalendar();
            e.stopPropagation();
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.calendar-popup') && 
                !e.target.closest('.date-picker')) {
                hideCalendar();
            }
        });

        // Initialize
        renderCalendar();

        // Thêm overlay vào body
        document.body.insertAdjacentHTML('beforeend', '<div class="overlay" id="overlay"></div>');

        document.addEventListener('DOMContentLoaded', function() {
            const counts = {
                room: 1,
                adult: 1,
                child: 0,
                infant: 0
            };

            const limits = {
                room: { min: 1, max: 5 },
                adult: { min: 1, max: 4 },
                child: { min: 0, max: 3 },
                infant: { min: 0, max: 2 }
            };

            function updateCount(type, change) {
                const newCount = counts[type] + change;
                const limit = limits[type];
                
                if (newCount >= limit.min && newCount <= limit.max) {
                    counts[type] = newCount;
                    document.getElementById(`${type}Count`).textContent = newCount;
                    updateButtonStates();
                    updateDisplayText();
                }
            }

            function updateButtonStates() {
                Object.keys(counts).forEach(type => {
                    const minusBtn = document.getElementById(`minus${type.charAt(0).toUpperCase() + type.slice(1)}`);
                    const plusBtn = document.getElementById(`plus${type.charAt(0).toUpperCase() + type.slice(1)}`);
                    
                    if (minusBtn && plusBtn) {
                        minusBtn.disabled = counts[type] <= limits[type].min;
                        plusBtn.disabled = counts[type] >= limits[type].max;
                    }
                });
            }

            function updateDisplayText() {
                const text = `${counts.room} Phòng - ${counts.adult} Người lớn - ${counts.child} Trẻ em - ${counts.infant} Em bé`;
                document.getElementById('selectedRooms').textContent = text;
            }

            // Thêm event listeners cho tất cả các nút
            const buttons = {
                Room: 'room',
                Adult: 'adult',
                Child: 'child',
                Infant: 'infant'
            };

            Object.entries(buttons).forEach(([key, value]) => {
                const minusBtn = document.getElementById(`minus${key}`);
                const plusBtn = document.getElementById(`plus${key}`);
                
                if (minusBtn && plusBtn) {
                    minusBtn.addEventListener('click', () => updateCount(value, -1));
                    plusBtn.addEventListener('click', () => updateCount(value, 1));
                }
            });

            // Khởi tạo trạng thái ban đầu
            updateButtonStates();
        });

        // Hiện/ẩn popup
        function showRoomPopup() {
            document.getElementById('roomPopup').classList.add('show');
            document.getElementById('overlay').classList.add('show');
        }

        function hideRoomPopup() {
            document.getElementById('roomPopup').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
        }

        // Event listeners cho popup
        document.querySelector('.room-picker').addEventListener('click', (e) => {
            showRoomPopup();
            e.stopPropagation();
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.room-popup') && 
                !e.target.closest('.room-picker')) {
                hideRoomPopup();
            }
        });

        function handleSearch(event) {
            event.preventDefault();
            
            // Lấy thông tin từ form
            const formData = {
                hotel: document.getElementById('selectedLocation').textContent,
                name: document.getElementById('customerName').value.trim(),
                phone: document.getElementById('phoneNumber').value.trim(),
                checkIn: document.getElementById('checkInDate')?.value || '',
                checkOut: document.getElementById('checkOutDate')?.value || '',
                rooms: document.getElementById('roomCount').textContent,
                adults: document.getElementById('adultCount').textContent,
                children: document.getElementById('childCount').textContent,
                infants: document.getElementById('infantCount').textContent,
                promoCode: document.getElementById('promoCode')?.value.trim() || ''
            };

            // Validation
            if (formData.hotel === 'Chọn địa điểm') {
                alert('Vui lòng chọn khách sạn');
                return false;
            }

            if (!formData.name) {
                alert('Vui lòng nhập họ tên');
                return false;
            }

            if (!formData.phone || !/^[0-9]{10}$/.test(formData.phone)) {
                alert('Vui lòng nhập số điện thoại hợp lệ (10 số)');
                return false;
            }

            // Gửi dữ liệu đến server
            fetch('save_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hiển thị thông báo thành công
                    const successDiv = document.getElementById('bookingSuccess');
                    successDiv.style.display = 'block';
                    
                    // Tự động ẩn sau 2 giây
                    setTimeout(() => {
                        successDiv.style.display = 'none';
                        // Reload trang
                        location.reload();
                    }, 2000);
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại sau.');
            });

            return false;
        }

        // Thêm validation cho số điện thoại
        document.getElementById('phoneNumber').addEventListener('input', function(e) {
            // Chỉ cho phép nhập số
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Giới hạn độ dài
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
    </script>
</body>
</html>