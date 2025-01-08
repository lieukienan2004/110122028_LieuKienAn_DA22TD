<?php
session_start();
require_once 'config/database.php';

// Lấy danh sách món ăn theo danh mục
function getMonAnByDanhMuc($conn, $danhMucId) {
    $sql = "SELECT * FROM mon_an WHERE danh_muc_id = :danh_muc_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':danh_muc_id', $danhMucId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy tất cả món ăn
function getAllMonAn($conn) {
    $sql = "SELECT mon_an.*, danh_muc.ten as ten_danh_muc 
            FROM mon_an 
            JOIN danh_muc ON mon_an.danh_muc_id = danh_muc.id";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function hienThiMonAn($monAn) {
    // Đảm bảo id tồn tại và là số nguyên
    $id = isset($monAn['id']) ? intval($monAn['id']) : 0;
    
    // Chỉ cần giữ lại phần địa điểm file
    switch($id) {
        case 1:
            $diadiemFile = '/KIENAN/dacdiem1.html';
            break;
        case 2:
            $diadiemFile = '/KIENAN/dacdiem2.html';
            break;
        case 3:
            $diadiemFile = '/KIENAN/dacdiem3.html';
            break;
        case 4:
            $diadiemFile = '/KIENAN/dacdiem4.html';
            break;
        case 5:
            $diadiemFile = '/KIENAN/dacdiem5.html';
            break;
        case 6:
            $diadiemFile = '/KIENAN/dacdiem6.html';
            break;
        case 7:
            $diadiemFile = '/KIENAN/dacdiem7.html';
            break;
        case 8:
                $diadiemFile = '/KIENAN/dacdiem8.html';
                break;
                case 9:
                    $diadiemFile = '/KIENAN/dacdiem9.html';
                    break;
                    case 10:
                        $diadiemFile = '/KIENAN/dacdiem10.html';
                        break;
                        case 11:
                            $diadiemFile = '/KIENAN/dacdiem11.html';
                            break;
                            case 12:
                                $diadiemFile = '/KIENAN/dacdiem12.html';
                                break;
                                case 13:
                                    $diadiemFile = '/KIENAN/dacdiem13.html';
                                    break;
                                    case 14:
                                        $diadiemFile = '/KIENAN/dacdiem14.html';
                                        break;
                                        case 15:
                                            $diadiemFile = '/KIENAN/dacdiem15.html';
                                            break;
                                            case 16:
                                                $diadiemFile = '/KIENAN/dacdiem16.html';
                                                break;
                                                case 17:
                                                    $diadiemFile = '/KIENAN/dacdiem17.html';
                                                    break;
                                                    case 18:
                                                        $diadiemFile = '/KIENAN/dacdiem18.html';
                                                        break;
                                                        case 19:
                                                            $diadiemFile = '/KIENAN/dacdiem19.html';
                                                            break;
                                                            case 20:
                                                                $diadiemFile = '/KIENAN/dacdiem20.html';
                                                                break;
                                                                case 21:
                                                                    $diadiemFile = '/KIENAN/dacdiem21.html';
                                                                    break;
                                                                    case 22:
                                                                        $diadiemFile = '/KIENAN/dacdiem22.html';
                                                                        break;
                                                                        case 23:
                                                                            $diadiemFile = '/KIENAN/dacdiem23.html';
                                                                            break;
                                                                            case 24:
                                                                                $diadiemFile = '/KIENAN/dacdiem24.html';
                                                                                break;
        default:
            $diadiemFile = '#';
            break;
    }
    
    echo '<div class="product" data-id="' . $id . '">';
    echo '<img src="images/' . htmlspecialchars($monAn['anh']) . '" alt="' . htmlspecialchars($monAn['ten']) . '">';
    // Thêm phần overlay thông tin
    echo '<div class="product-info">';
    echo '<h3>' . htmlspecialchars($monAn['ten']) . '</h3>';
    if (!empty($monAn['mo_ta'])) {
        echo '<p>' . htmlspecialchars($monAn['mo_ta']) . '</p>';
    }
    echo '</div>';
    echo '<h2>' . htmlspecialchars($monAn['ten']) . '</h2>';
    if (!empty($monAn['mo_ta'])) {
        echo '<p>' . htmlspecialchars($monAn['mo_ta']) . '</p>';
    }
    echo '<div class="button-container">';
    echo '<div class="button-row">';
    echo '<a href="javascript:void(0)" class="action-button details-btn" onclick="showDacDiem(' . $id . ')">Đặc Điểm</a>';
    echo '<a href="' . $diadiemFile . '" class="action-button location-btn">Địa Điểm</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

// Thêm hàm mới để lấy đặc điểm món ăn
function getDacDiemMonAn($conn, $monAnId) {
    $sql = "SELECT mo_ta, gia, chi_tiet FROM mon_an WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $monAnId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Thêm hàm tìm kiếm món ăn
function searchMonAn($conn, $keyword) {
    $sql = "SELECT mon_an.*, danh_muc.ten as ten_danh_muc 
            FROM mon_an 
            JOIN danh_muc ON mon_an.danh_muc_id = danh_muc.id
            WHERE mon_an.ten LIKE :keyword 
            OR mon_an.mo_ta LIKE :keyword";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$keyword}%";
    $stmt->bindParam(':keyword', $searchTerm);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Cập nhật hàm luuDanhGia
function luuDanhGia($conn, $soSao, $noiDung, $userId) {
    try {
        $sql = "INSERT INTO danh_gia (so_sao, noi_dung, user_id) VALUES (:so_sao, :noi_dung, :user_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':so_sao', $soSao);
        $stmt->bindParam(':noi_dung', $noiDung);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Thêm hàm lấy danh sách đánh giá
function getDanhGia($conn) {
    $sql = "SELECT danh_gia.*, users.username, users.avatar 
            FROM danh_gia 
            JOIN users ON danh_gia.user_id = users.id 
            ORDER BY danh_gia.ngay_tao DESC";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function phan_loai_sao($so_sao) {
    if (!is_numeric($so_sao) || $so_sao < 0 || $so_sao > 5) {
        return "Đánh giá không hợp lệ";
    }
    
    if ($so_sao == 5) {
        return "Xuất sắc";
    } elseif ($so_sao >= 4.5) {
        return "Rất tốt";
    } elseif ($so_sao >= 3.7) {
        return "Tốt";
    } elseif ($so_sao >= 2.8) {
        return "Trung bình";
    } elseif ($so_sao >= 1.5) {
        return "Kém";
    } else {
        return "Rất kém";
    }
}

// Thêm hàm lưu liên hệ
function luuLienHe($conn, $hoTen, $email, $soDienThoai, $noiDung) {
    try {
        $sql = "INSERT INTO lien_he (ho_ten, email, so_dien_thoai, noi_dung) 
                VALUES (:ho_ten, :email, :so_dien_thoai, :noi_dung)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':ho_ten', $hoTen);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':so_dien_thoai', $soDienThoai);
        $stmt->bindParam(':noi_dung', $noiDung);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Cập nhật phần hiển thị đánh giá trong reviews-list
function hienThiDanhGia($danhGia) {
    echo '<div class="review-item">';
    echo '<div class="review-header">';
    // Thêm container cho avatar và username
    echo '<div class="user-info">';
    echo '<img src="avatars/' . htmlspecialchars($danhGia['avatar']) . '" alt="Avatar" class="review-avatar">';
    echo '<span class="username">' . htmlspecialchars($danhGia['username']) . '</span>';
    echo '</div>';
    
    echo '<div class="stars-display">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $danhGia['so_sao']) {
            echo '<span class="star filled">★</span>';
        } else {
            echo '<span class="star">☆</span>';
        }
    }
    echo '</div>';
    
    echo '<span class="date">' . date('d/m/Y H:i', strtotime($danhGia['ngay_tao'])) . '</span>';
    echo '</div>';
    echo '<div class="review-content">';
    echo htmlspecialchars($danhGia['noi_dung']);
    echo '</div>';
    echo '</div>';
}

// Thêm CSS mới

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu Đặc Sản Trà Vinh</title>
    <link rel="icon" type="image/x-icon" href="images/kienan.jpg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #343a40;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #8B4513;
            color: white;
            padding: 15px 30px;
            animation: slideDown 0.5s ease;
            background: linear-gradient(to bottom, #8B4513, #A0522D);
            border-bottom: 2px solid rgba(245, 222, 179, 0.3);
        }
        #icon {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            background-color: #8B4513;
        }
        nav ul li {
            display: inline;
            margin-left: 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .search-form {
            display: flex;
            align-items: center;
        }
        .search-form input {
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-right: 5px;
        }
        .menu {
            background-color: #e9ecef;
            padding: 10px;
            text-align: center;
        }
        .menu h2 {
            margin: 0;
            font-size: 1.5em;
        }
        .menu a {
            margin: 0 15px;
            text-decoration: none;
            color: #007bff;
        }
        .menu a:hover {
            text-decoration: underline;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }
        .product {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            margin: 15px;
            width: 280px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        .product img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .product h2 {
            color: #333;
            font-size: 18px;
            margin: 10px 0;
        }
        .product p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 20px;
        }
        .highlight {
            color: #007BFF; /* Màu xanh nước biển */
        }
        .rating {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .rating input {
            display: none;
        }
        .rating label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
        }
        .rating input:checked ~ label {
            color: #ffcc00;
        }
		.menu {
    background-color: #e9ecef;
    padding: 20px;
    text-align: center; /* Căn giữa toàn bộ menu */
}

.menu h2 {
    margin: 0;
    font-size: 1.5em;
}

.menu-links {
    display: flex; /* Sử dụng flexbox để căn giữa các liên kết */
    justify-content: center; /* Căn giữa các liên kết */
    margin-top: 10px; /* Khoảng cách giữa tiêu đề và các liên kết */
}

.menu-links a {
    margin: 0 20px; /* Khoảng cách giữa các liên kết */
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
}

.menu-links a:hover {
    text-decoration: underline; /* Gạch chân khi hover */
}

.login-prompt {
    text-align: center;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 4px;
    margin: 20px auto;
    max-width: 400px;
}

.login-prompt a {
    color: #ff3333;
    text-decoration: none;
    font-weight: bold;
}
.login-prompt p {
    color: #000000;
    text-decoration: none;
    font-weight: bold;
}

.login-prompt a:hover {
    text-decoration: underline;
}

.auth-btn {
    background-color: #45a049;
    color: white;
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.auth-btn:hover {
    background-color: #357a38;
}

.welcome-text {
    color: white;
    margin-right: 15px;
}

nav ul {
    display: flex;
    align-items: center;
    gap: 15px;
}

nav ul li {
    list-style: none;
}
.button-container {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0 15px;
}

.button-row {
    display: flex;
    justify-content: space-between;
    gap: 15px;
}

.action-button {
    flex: 1;
    display: inline-block;
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.details-btn {
    background-color: #4CAF50;
    color: white;
    border: 2px solid #4CAF50;
}

.details-btn:hover {
    background-color: white;
    color: #4CAF50;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
}

.location-btn {
    background-color: #2196F3;
    color: white;
    border: 2px solid #2196F3;
}

.location-btn:hover {
    background-color: white;
    color: #2196F3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
}

.reviews-container {
    max-width: 1000px;
    margin: 50px auto;
    padding: 30px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.reviews-container h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
    font-size: 24px;
    position: relative;
    padding-bottom: 10px;
}

.reviews-container h2:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background-color: #4CAF50;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.review-item {
    background-color: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
    margin-bottom: 20px;
    opacity: 0;
    transform: translateX(-20px);
    animation: slideIn 0.5s ease forwards;
}

.review-item:hover {
    transform: translateY(-2px);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.username {
    font-weight: bold;
    color: #4CAF50;
    font-size: 16px;
}

.rating {
    color: #ffd700;
    letter-spacing: 3px;
    font-size: 18px;
}

.date {
    color: #888;
    font-size: 14px;
}

.review-content {
    color: #444;
    line-height: 1.6;
    font-size: 15px;
    padding: 15px 0;
}

/* Style cho form đánh giá */
#danhGiaForm {
    background-color: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

#danhGiaForm h3 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
    font-size: 20px;
}

.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
    margin-bottom: 25px;
}

.rating-stars input[type="radio"] {
    display: none;
}

.rating-stars label {
    cursor: pointer;
    font-size: 35px;
    color: #ddd;
    padding: 0 5px;
    transition: all 0.2s ease;
}

.rating-stars label:hover,
.rating-stars label:hover ~ label,
.rating-stars input[type="radio"]:checked ~ label {
    color: #ffd700;
}

.review-form-content {
    width: 100%;
}

.review-form-content textarea {
    width: 100%;
    min-height: 150px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 15px;
    resize: vertical;
    transition: border-color 0.3s ease;
    line-height: 1.6;
}

.review-form-content textarea:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
}

.review-form-content button {
    display: block;
    width: 200px;
    margin: 0 auto;
    padding: 12px 0;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.review-form-content button:hover {
    background-color: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.login-prompt {
    text-align: center;
    padding: 30px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin: 30px auto;
    max-width: 400px;
}

.login-prompt p {
    color: #555;
    font-size: 16px;
    margin: 0;
}

.login-prompt a {
    color: #4CAF50;
    text-decoration: none;
    font-weight: 500;
}

.login-prompt a:hover {
    text-decoration: underline;
}

.contact-section {
    padding: 60px 20px;
    background-color: #f9f9f9;
    margin-top: 40px;
}

.contact-section h2 {
    text-align: center;
    color: #333;
    margin-bottom: 40px;
    font-size: 2em;
}

.contact-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
}

.contact-info, .contact-form {
    flex: 1;
    min-width: 300px;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.contact-info h3, .contact-form h3 {
    color: #4CAF50;
    margin-bottom: 25px;
}

.info-item {
    display: flex;
    align-items: start;
    margin-bottom: 20px;
}

.info-item i {
    color: #4CAF50;
    font-size: 20px;
    margin-right: 15px;
    margin-top: 3px;
}

.info-item p {
    margin: 0;
    color: #666;
    line-height: 1.6;
}

.social-links {
    margin-top: 30px;
    display: flex;
    gap: 15px;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #4CAF50;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.social-link:hover {
    transform: translateY(-3px);
}

.form-group {
    margin-bottom: 20px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.form-group textarea {
    height: 150px;
    resize: vertical;
}

.submit-btn {
    background: #4CAF50;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.submit-btn:hover {
    background: #45a049;
}

.spinner {
    display: inline-block;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .contact-container {
        flex-direction: column;
    }
    
    .contact-info, .contact-form {
        width: 100%;
    }
}

/* Cập nhật style cho form tìm kiếm */
.search-form {
    margin-left: 20px;
}

.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    max-width: 300px;
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(245, 222, 179, 0.3);
}

.search-wrapper input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: none;
    border-radius: 25px;
    background: transparent;
    color: #F5DEB3;
    font-size: 14px;
    transition: all 0.3s ease;
}

.search-wrapper input::placeholder {
    color: rgba(245, 222, 179, 0.7);
}

.search-wrapper input:focus {
    background-color: rgba(255, 255, 255, 0.2);
    outline: none;
    border-color: rgba(255, 255, 255, 0.5);
}

.search-wrapper button {
    position: absolute;
    right: 5px;
    padding: 8px 12px;
    background: none;
    border: none;
    color: #F5DEB3;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.search-wrapper button:hover {
    transform: scale(1.1);
}

/* Style cho kết quả tìm kiếm */
.search-results {
    margin: 30px auto;
    text-align: center;
}

.search-results h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 20px;
    position: relative;
    display: inline-block;
}

.search-results h2:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 3px;
    background: #4CAF50;
}

.no-results {
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    color: #666;
    font-size: 16px;
    margin: 20px auto;
    max-width: 500px;
}

.no-results i {
    display: block;
    font-size: 40px;
    color: #4CAF50;
    margin-bottom: 15px;
}

/* Thêm hiệu ứng cho header */
header {
    animation: slideDown 0.5s ease;
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Hiệu ứng cho các sản phẩm */
.product {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hiệu ứng hover cho nút tìm kiếm */
.search-wrapper button {
    transition: all 0.3s ease;
}

.search-wrapper button:hover {
    transform: scale(1.2) rotate(360deg);
}

/* Hiệu ứng cho các đánh giá */
.review-item {
    opacity: 0;
    transform: translateX(-20px);
    animation: slideIn 0.5s ease forwards;
}

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Hiệu ứng cho form liên hệ */
.contact-form input,
.contact-form textarea {
    transition: all 0.3s ease;
}

.contact-form input:focus,
.contact-form textarea:focus {
    transform: scale(1.02);
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.3);
}

/* Hiệu ứng cho social links */
.social-link {
    transition: all 0.3s ease;
}

.social-link:hover {
    transform: translateY(-5px) rotate(360deg);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Hiệu ứng loading cho nút submit */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Hiệu ứng cho tiêu đề các phần */
.container h2 {
    position: relative;
    padding-bottom: 10px;
    margin-bottom: 30px;
}

.container h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 3px;
    background: #4CAF50;
    animation: expandWidth 0.5s ease forwards;
}

@keyframes expandWidth {
    to {
        width: 100px;
    }
}

/* Hiệu ứng cho menu */
.menu-links a {
    position: relative;
    overflow: hidden;
}

.menu-links a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #4CAF50;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.menu-links a:hover::after {
    transform: translateX(0);
}

/* Hiệu ứng cho rating stars */
.rating-stars label {
    transition: transform 0.2s ease;
}

.rating-stars label:hover {
    transform: scale(1.3) rotate(15deg);
}

/* Thêm hiệu ứng hover cho hình ảnh sản phẩm */
.product {
    position: relative;
    overflow: hidden;
}

.product img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 15px;
    transition: all 0.5s ease;
}

/* Hiệu ứng zoom khi hover */
.product:hover img {
    transform: scale(1.1);
}

/* Thêm overlay với thông tin khi hover */
.product::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 200px; /* Chiều cao bằng với ảnh */
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 10px;
}

.product:hover::before {
    opacity: 1;
}

.product-info {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    text-align: center;
    width: 90%;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 1;
}

.product:hover .product-info {
    opacity: 1;
}

.product-info h3 {
    font-size: 1.2em;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.product-info p {
    font-size: 0.9em;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Căn giữa tất cả text trong product */
.product {
    text-align: center;  /* Căn giữa tất cả text */
}

.product h2 {
    text-align: center;
    width: 100%;
    margin: 15px 0;
    font-size: 18px;
    color: #333;
}

.product p {
    text-align: center;
    width: 100%;
    color: #666;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

/* Căn giữa text trong overlay khi hover */
.product-info {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    text-align: center;
    width: 90%;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 1;
}

.product-info h3 {
    text-align: center;
    font-size: 1.2em;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.product-info p {
    text-align: center;
    font-size: 0.9em;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Căn giữa các nút */
.button-container {
    text-align: center;
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 0 15px;
}

.button-row {
    display: flex;
    justify-content: center;
    gap: 15px;
    width: 100%;
}

.action-button {
    flex: 1;
    text-align: center;
    max-width: 120px; /* Giới hạn chiều rộng tối đa của nút */
}

/* Cập nhật style cho modal */
.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 25px;
    border: none;
    width: 90%;
    max-width: 600px;
    border-radius: 15px;
    position: relative;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal h2 {
    color: #4CAF50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #4CAF50;
}

.chi-tiet-content {
    line-height: 1.8;
    color: #333;
    font-size: 16px;
    padding: 15px 0;
}

.chi-tiet-content strong {
    color: #4CAF50;
    display: block;
    margin-bottom: 8px;
}

.close {
    position: absolute;
    right: 20px;
    top: 15px;
    color: #666;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: #4CAF50;
}

/* Thêm CSS vào phần <style> */
.modal {
    display: block;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 25px;
    border: none;
    width: 90%;
    max-width: 600px;
    border-radius: 15px;
    position: relative;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    animation: slideIn 0.3s ease;
}

.close {
    position: absolute;
    right: 20px;
    top: 15px;
    color: #666;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: #4CAF50;
}

.chi-tiet-content {
    line-height: 1.8;
    color: #333;
    font-size: 16px;
    padding: 15px 0;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        transform: translateY(-100px);
        opacity: 0;
    }
    to { 
        transform: translateY(0);
        opacity: 1;
    }
}

/* Thêm vào phần style */
.stars-display {
    display: flex;
    gap: 5px;
    align-items: center;
}

.star {
    font-size: 20px;
    color: #
}

.qr-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.qr-section h3 {
    color: #4CAF50;
    margin-bottom: 20px;
    text-align: center;
}

.qr-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.qr-image {
    width: 200px;
    height: 200px;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.qr-image:hover {
    transform: scale(1.05);
}

.website-info {
    text-align: center;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    width: 100%;
}

.website-info p {
    margin: 8px 0;
    color: #333;
}

.website-info strong {
    color: #4CAF50;
}

.website-info i {
    font-size: 0.9em;
    color: #666;
}

/* Media query cho màn hình nhỏ */
@media (max-width: 768px) {
    .qr-image {
        width: 150px;
        height: 150px;
    }
    
    .website-info {
        padding: 10px;
    }
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.review-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #4CAF50;
}

.username {
    font-weight: bold;
    color: #4CAF50;
    font-size: 16px;
}

/* Thêm/cập nhật CSS cho dropdown */
.dropbtn {
    color: #F5DEB3;
    padding: 10px 15px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    background: none;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 5px;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #8B4513;
    min-width: 200px; /* Tăng độ rộng tối thiểu */
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: #F5DEB3;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    border-bottom: 1px solid rgba(245, 222, 179, 0.1);
    white-space: nowrap; /* Ngăn text bị ngắt dòng */
}

/* Hiệu ứng hover */
.dropdown:hover .dropdown-content {
    display: block;
}

.dropdown-content a:hover {
    background-color: #A0522D;
    color: #FFE4B5;
    padding-left: 25px;
}

/* Thêm mũi tên cho dropdown button */
.dropbtn::after {
    content: '▼';
    margin-left: 5px;
    font-size: 12px;
}

/* Cập nhật màu cho các nút auth */
.auth-btn {
    color: #F5DEB3;
    transition: color 0.3s ease;
}

.auth-btn:hover {
    color: #FFE4B5;
}

/* Cập nhật màu cho thanh tìm kiếm */
.search-wrapper {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(245, 222, 179, 0.3);
}

.search-wrapper input {
    color: #F5DEB3;
    background: transparent;
}

.search-wrapper input::placeholder {
    color: rgba(245, 222, 179, 0.7);
}

.search-wrapper button {
    color: #F5DEB3;
}

.search-wrapper button:hover {
    color: #FFE4B5;
}

/* Thêm gradient và border cho header */
header {
    background: linear-gradient(to bottom, #8B4513, #A0522D);
    border-bottom: 2px solid rgba(245, 222, 179, 0.3);
}

/* Hiệu ứng hover mượt mà */
.dropdown-content a:hover {
    background: linear-gradient(to right, #8B4513, #A0522D);
    padding-left: 25px;
}

.review-form-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 30px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.review-form-container h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
    font-size: 24px;
    position: relative;
}

.review-form-container h2:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: #4CAF50;
}

.modern-review-form {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.rating-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    gap: 8px;
}

.rating-stars input {
    display: none;
}

.rating-stars label {
    font-size: 35px;
    color: #ddd;
    cursor: pointer;
    transition: all 0.2s ease;
}

.rating-stars label:hover,
.rating-stars label:hover ~ label,
.rating-stars input:checked ~ label {
    color: #ffd700;
    transform: scale(1.1);
}

.rating-text {
    color: #666;
    font-size: 14px;
}

.form-group {
    position: relative;
}

.form-group textarea {
    width: 100%;
    min-height: 150px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    line-height: 1.6;
    resize: vertical;
    transition: all 0.3s ease;
}

.form-group textarea:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.1);
    outline: none;
}

.textarea-footer {
    display: flex;
    justify-content: flex-end;
    padding: 8px;
}

.char-counter {
    color: #666;
    font-size: 12px;
}

.submit-review-btn {
    align-self: center;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 30px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.submit-review-btn:hover {
    background: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
}

.button-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.loading-spinner {
    display: none;
    animation: spin 1s linear infinite;
}

.submit-review-btn.loading .button-content {
    display: none;
}

.submit-review-btn.loading .loading-spinner {
    display: block;
}

.login-prompt {
    text-align: center;
    padding: 40px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px dashed #dee2e6;
}

.login-prompt i {
    font-size: 40px;
    color: #4CAF50;
    margin-bottom: 15px;
}

.login-prompt p {
    color: #666;
    margin: 0;
}

.login-prompt a {
    color: #4CAF50;
    text-decoration: none;
    font-weight: 500;
}

.login-prompt a:hover {
    text-decoration: underline;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .review-form-container {
        padding: 20px;
        margin: 20px;
    }
    
    .rating-stars label {
        font-size: 30px;
    }
}

h1 {
    position: relative;
    color: #F5DEB3;
    font-size: 2.5em;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    animation: titleEffect 1.5s ease-out;
    padding-bottom: 10px;
}

h1::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 3px;
    background: #F5DEB3;
    animation: underlineEffect 1.5s ease-out forwards 0.5s;
}

@keyframes titleEffect {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes underlineEffect {
    0% {
        width: 0;
    }
    100% {
        width: 80%;
    }
}

/* Thêm hiệu ứng hover */
h1:hover {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

/* Thêm hiệu ứng chữ phát sáng khi hover */
h1:hover {
    text-shadow: 0 0 10px rgba(245, 222, 179, 0.8),
                 0 0 20px rgba(245, 222, 179, 0.5),
                 0 0 30px rgba(245, 222, 179, 0.3);
    transition: text-shadow 0.3s ease;
}

/* Thêm CSS cho notification bell */
.notification-bell {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
}

.bell-button {
    width: 80px;  /* Tăng từ 60px lên 80px */
    height: 80px; /* Tăng từ 60px lên 80px */
    background-color: #4CAF50;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.bell-button:hover {
    transform: scale(1.1);
    background-color: #45a049;
}

.bell-icon {
    color: white;
    font-size: 32px; /* Tăng từ 24px lên 32px */
    animation: bellRing 2s infinite;
}

@keyframes bellRing {
    0% { transform: rotate(0); }
    10% { transform: rotate(15deg); }
    20% { transform: rotate(-15deg); }
    30% { transform: rotate(15deg); }
    40% { transform: rotate(-15deg); }
    50% { transform: rotate(0); }
    100% { transform: rotate(0); }
}
    </style>
</head>
<body>
<header>
    <img src="./images/kienan.jpg" id="icon">
    <h1>Giới Thiệu Đặc Sản Trà Vinh</h1>
    <nav>
        <ul>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn">Danh mục</a>
                <div class="dropdown-content">
                    <a href="#mon-an" onclick="scrollToSection('mon-an')">Món ăn</a>
                    <a href="#nuoc-uong" onclick="scrollToSection('nuoc-uong')">Đặc sản làm quà</a>
                    <a href="#do-an-vat" onclick="scrollToSection('do-an-vat')">Đồ ăn vặt</a>
                </div>
            </li>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li><a href="dangky.php" class="auth-btn">Đăng ký</a></li>
                <li><a href="dangnhap.php" class="auth-btn">Đăng nhập</a></li>
            <?php else: ?>
                <li><span class="welcome-text">Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <li><a href="thaydoithongtin.php" class="auth-btn">Thay đổi thông tin</a></li>
                <li><a href="dangxuat.php" class="auth-btn">Đăng xuất</a></li>
            <?php endif; ?>
            <li><a href="gioithieu.html">Giới thiệu</a></li>
            <li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#lien-he" class="auth-btn" onclick="scrollToContact(event)">Liên hệ</a>
                <?php else: ?>
                    <a href="javascript:void(0)" onclick="alertLogin()" class="auth-btn">Liên hệ</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
    <form class="search-form" method="GET" action="">
        <div class="search-wrapper">
            <input type="text" name="search" placeholder="Tìm kiếm món ăn..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
</header>

    <!-- Phần hiển thị kết quả tìm kiếm và món ăn -->
    <div class="container" id="mon-an">
        <?php
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            echo '<div class="search-results">';
            echo '<h2>Kết Quả Tìm Kiếm</h2>';
            $ketQuaTimKiem = searchMonAn($conn, $_GET['search']);
            if (empty($ketQuaTimKiem)) {
                echo '<div class="no-results">';
                echo '<i class="bi bi-search"></i>';
                echo '<p>Không tìm thấy món ăn nào phù hợp với từ khóa "' . htmlspecialchars($_GET['search']) . '"</p>';
                echo '</div>';
            } else {
                foreach ($ketQuaTimKiem as $item) {
                    hienThiMonAn($item);
                }
            }
            echo '</div>';
        } else {
            // Hiển thị nội dung mặc định như cũ
            echo '<h2 style="width: 100%; text-align: center;">Món Ăn</h2>';
            $monAn = getMonAnByDanhMuc($conn, 1);
            foreach ($monAn as $item) {
                hienThiMonAn($item);
            }
        }
        ?>
    </div>

    <!-- Phần đặc sản làm quà -->
    <div class="container" id="nuoc-uong">
        <h2 style="width: 100%; text-align: center;">Đặc Sản Làm Quà</h2>
        <?php
        $dacSanLamQua = getMonAnByDanhMuc($conn, 2); // 2 là ID của danh mục đặc sản làm quà
        foreach ($dacSanLamQua as $item) {
            hienThiMonAn($item);
        }
        ?>
    </div>

    <!-- Phần đồ ăn vặt -->
    <div class="container" id="do-an-vat">
        <h2 style="width: 100%; text-align: center;">Đồ Ăn Vặt</h2>
        <?php
        $doAnVat = getMonAnByDanhMuc($conn, 3); // 3 là ID của danh mục đồ ăn vặt
        foreach ($doAnVat as $item) {
            hienThiMonAn($item);
        }
        ?>
    </div>

    <!-- Phần hiển thị đánh giá -->
    <div class="reviews-container">
        <h2>Đánh giá từ người dùng</h2>
        <div class="reviews-list">
            <?php
            $danhGiaList = getDanhGia($conn);
            foreach ($danhGiaList as $danhGia) {
                hienThiDanhGia($danhGia);
            }
            ?>
        </div>
    </div>

    <!-- Phần form đánh giá -->
    <div class="review-form-container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <form id="danhGiaForm" class="modern-review-form">
                <div class="rating-container">
                    <div class="rating-stars">
                        <input type="radio" id="star5" name="rating" value="5" required>
                        <label for="star5" title="Xuất sắc">★</label>
                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4" title="Rất tốt">★</label>
                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3" title="Tốt">★</label>
                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2" title="Trung bình">★</label>
                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1" title="Kém">★</label>
                    </div>
                    <div class="rating-text">Chọn đánh giá của bạn</div>
                </div>

                <div class="form-group">
                    <textarea 
                        name="noiDung" 
                        id="noiDung" 
                        placeholder="Chia sẻ trải nghiệm của bạn về đặc sản Trà Vinh..." 
                        required
                    ></textarea>
                    <div class="textarea-footer">
                        <span class="char-counter">0/1000</span>
                    </div>
                </div>

                <button type="submit" class="submit-review-btn">
                    <span class="button-content">
                        <i class="bi bi-send"></i>
                        <span>Gửi đánh giá</span>
                    </span>
                    <span class="loading-spinner">
                        <i class="bi bi-arrow-repeat"></i>
                    </span>
                </button>
            </form>
        <?php else: ?>
            <div class="login-prompt">
                <i class="bi bi-person-circle"></i>
                <p>Vui lòng <a href="dangnhap.php">đăng nhập</a> để chia sẻ đánh giá của bạn</p>
            </div>
        <?php endif; ?>
    </div>
</footer>

<div class="contact-section">
    <h2>Liên Hệ</h2>
    <div class="contact-container">
        <!-- Thông tin liên hệ -->
        <div class="contact-info">
            <h3>Thông Tin Liên Hệ</h3>
            <div class="info-item">
                <i class="bi bi-geo-alt"></i>
                <p>Địa chỉ: 126 Nguyễn Thiện Thành, Phường 5, TP. Trà Vinh</p>
            </div>
            <div class="info-item">
                <i class="bi bi-telephone"></i>
                <p>Điện thoại: 0912 4317 19</p>
            </div>
            <div class="info-item">
                <i class="bi bi-envelope"></i>
                <p>Email: lieukienanan@gmail.com</p>
            </div>
            <div class="info-item">
                <i class="bi bi-clock"></i>
                <p>Giờ làm việc: 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>
            </div>
            
            
            
            <!-- Thêm phần QR code -->
            <div class="qr-section">
                <h3>Quét mã QR để truy cập website</h3>
                <div class="qr-container">
                    <img src="images/qr-web.jpg" alt="QR Code Website" class="qr-image">
                    <div class="website-info">
                        <p><strong>Website:</strong> Đặc sản Trà Vinh</p>
                        <p><strong>Địa chỉ:</strong> http://localhost:81/KienAn/</p>
                        <p><i>Quét mã QR để truy cập nhanh website trên điện thoại</i></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form liên hệ hiện tại -->
        <div class="contact-form" id="lien-he">
            <h3>Gửi Tin Nhắn</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form id="contactForm">
                    <div class="form-group">
                        <input type="text" name="hoTen" placeholder="Họ và tên *" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email *" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="soDienThoai" placeholder="Số điện thoại">
                    </div>
                    <div class="form-group">
                        <textarea name="noiDung" placeholder="Nội dung tin nhắn *" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">
                        <span class="button-text">Gửi tin nhắn</span>
                        <span class="spinner" style="display: none;">
                            <i class="bi bi-arrow-repeat"></i>
                        </span>
                    </button>
                </form>
            <?php else: ?>
                <div class="login-prompt">
                    <p>Vui lòng <a href="dangnhap.php">Đăng nhập</a> để gửi tin nhắn</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer style="background-color: #6B4B3E; color: white; padding: 40px 0;">
    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; padding: 0 20px;">
        <!-- Cột 1: Đặc sản Trà Vinh -->
        <div>
            <h3 style="font-size: 24px; margin-bottom: 20px;">Đặc sản Trà Vinh</h3>
            <p style="margin-bottom: 10px;">
                <i class="bi bi-telephone"></i> 0912 4317 19
            </p>
            <p style="margin-bottom: 10px;">
                <i class="bi bi-envelope"></i> lieukienanan@gmail.com
            </p>
            <p style="margin-bottom: 10px;">
                <i class="bi bi-geo-alt"></i> Khóm 6 Thị Trấn Càng Long, TP. Trà Vinh
            </p>
        </div>

        <!-- Cột 2: Liên kết nhanh -->
        <div>
            <h3 style="font-size: 24px; margin-bottom: 20px;">Liên kết nhanh</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 10px;"><a href="#mon-an" style="color: white; text-decoration: none;">Món ăn</a></li>
                <li style="margin-bottom: 10px;"><a href="#dac-san-lam-qua" style="color: white; text-decoration: none;">Đặc sản làm quà</a></li>
                <li style="margin-bottom: 10px;"><a href="#do-an-vat" style="color: white; text-decoration: none;">Đồ ăn vặt</a></li>
                <li style="margin-bottom: 10px;"><a href="#lien-he" style="color: white; text-decoration: none;">Liên hệ</a></li>
            </ul>
        </div>

        <!-- Cột 3: Kết nối với chúng tôi -->
        <div>
            <h3 style="font-size: 24px; margin-bottom: 20px;">Kết nối với chúng tôi</h3>
            <div style="display: flex; gap: 15px;">
                <a href="#" style="color: white; font-size: 24px;"><i class="bi bi-facebook"></i></a>
                <a href="#" style="color: white; font-size: 24px;"><i class="bi bi-instagram"></i></a>
                <a href="#" style="color: white; font-size: 24px;"><i class="bi bi-twitter"></i></a>
                <a href="#" style="color: white; font-size: 24px;"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
        <p>© 2024 Đặc sản Trà Vinh. All rights reserved.</p>
        <div style="margin-top: 10px;">
            <a href="#" style="color: white; text-decoration: none; margin: 0 10px;">Bảo mật thông tin</a>
            <a href="#" style="color: white; text-decoration: none; margin: 0 10px;">Liên hệ</a>
        </div>
    </div>
</footer>

<script>
document.getElementById('danhGiaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('luu-danh-gia.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const reviewsList = document.querySelector('.reviews-list');
            const newReview = document.createElement('div');
            newReview.className = 'review-item';
            
            const stars = '★'.repeat(formData.get('rating')) + '☆'.repeat(5 - formData.get('rating'));
            const now = new Date();
            const currentDate = now.toLocaleString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const username = '<?php echo isset($_SESSION["username"]) ? $_SESSION["username"] : ""; ?>';
            const userAvatar = '<?php echo isset($_SESSION["avatar"]) ? $_SESSION["avatar"] : "default-avatar.jpg"; ?>';
            
            newReview.innerHTML = `
                <div class="review-header">
                    <div class="user-info">
                        <img src="avatars/${userAvatar}" alt="Avatar" class="review-avatar">
                        <span class="username">${username}</span>
                    </div>
                    <div class="stars-display">
                        ${stars.split('').map(star => `<span class="star ${star === '★' ? 'filled' : ''}">${star}</span>`).join('')}
                    </div>
                    <span class="date">${currentDate}</span>
                </div>
                <div class="review-content">
                    ${formData.get('noiDung')}
                </div>
            `;
            
            reviewsList.insertBefore(newReview, reviewsList.firstChild);
            this.reset();
            alert('Cảm ơn bạn đã đánh giá!');
        } else {
            alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại sau.');
    });
});

function checkAndRedirect(file, type) {
    if (file === '#') {
        alert('Không có thông tin ' + type + ' cho món ăn này');
        return false;
    }
    return true;
}

// Thay thế hàm showDacDiem trong phần <script>
function showDacDiem(monAnId) {
    fetch(`get_dac_diem.php?id=${monAnId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Đặc điểm món ăn</h2>
                        <div class="chi-tiet-content">
                            <p><strong>Chi tiết:</strong> ${data.chi_tiet || 'Không có thông tin'}</p>
                            <p><strong>Giá:</strong> ${data.gia ? formatPrice(data.gia) : 'Chưa có giá'}</p>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);

                const closeBtn = modal.querySelector('.close');
                closeBtn.onclick = function() {
                    modal.remove();
                }

                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.remove();
                    }
                }
            } else {
                alert(data.message || 'Không có thông tin chi tiết cho món ăn này');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải thông tin chi tiết');
        });
}

// Thêm hàm format giá tiền
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

function alertLogin() {
    alert('Vui lòng đăng nhập để sử dụng chức năng liên hệ!');
    window.location.href = 'dangnhap.php';
}

// Thêm xử lý form liên hệ
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('.submit-btn');
    const buttonText = submitBtn.querySelector('.button-text');
    const spinner = submitBtn.querySelector('.spinner');
    
    // Hiển thị loading
    buttonText.style.display = 'none';
    spinner.style.display = 'inline-block';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('luu-lien-he.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cảm ơn bạn đã gửi tin nhắn!');
            this.reset();
        } else {
            alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại sau.');
    })
    .finally(() => {
        // Ẩn loading
        buttonText.style.display = 'inline-block';
        spinner.style.display = 'none';
        submitBtn.disabled = false;
    });
});

// Thêm hàm để scroll đến form liên hệ
function scrollToContact(event) {
    if (event) {
        event.preventDefault();
    }
    document.getElementById('lien-he').scrollIntoView({ behavior: 'smooth' });
}

// Thêm hiệu ứng xuất hiện khi scroll
function revealOnScroll() {
    const elements = document.querySelectorAll('.product, .review-item');
    elements.forEach((element, index) => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementTop < windowHeight - 50) {
            element.style.animation = `fadeInUp 0.6s ease forwards ${index * 0.1}s`;
        }
    });
}

// Gọi hàm khi scroll
window.addEventListener('scroll', revealOnScroll);
// Gọi lần đầu khi tải trang
revealOnScroll();

// Thêm hiệu ứng ripple cho các nút
function addRippleEffect(event) {
    const button = event.currentTarget;
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    
    ripple.style.position = 'absolute';
    ripple.style.borderRadius = '50%';
    ripple.style.width = ripple.style.height = '100px';
    ripple.style.left = `${event.clientX - rect.left - 50}px`;
    ripple.style.top = `${event.clientY - rect.top - 50}px`;
    ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
    ripple.style.transform = 'scale(0)';
    ripple.style.animation = 'ripple 0.6s linear';
    
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Thêm hiệu ứng ripple cho tất cả các nút
document.querySelectorAll('.action-button, .submit-btn').forEach(button => {
    button.addEventListener('click', addRippleEffect);
});

// Thêm keyframe cho hiệu ứng ripple
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Thêm hàm scroll đến section
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const headerHeight = document.querySelector('header').offsetHeight;
        const elementPosition = section.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerHeight;

        window.scrollTo({
            top: offsetPosition,
            behavior: "smooth"
        });
    }
}

// Thêm active class cho menu item khi scroll
window.addEventListener('scroll', function() {
    const sections = ['mon-an', 'nuoc-uong', 'do-an-vat'];
    const headerHeight = document.querySelector('header').offsetHeight;
    
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            const sectionTop = section.offsetTop - headerHeight - 100;
            const sectionBottom = sectionTop + section.offsetHeight;
            
            if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionBottom) {
                document.querySelectorAll('.dropdown-content a').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + sectionId) {
                        link.classList.add('active');
                    }
                });
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const showReviewsBtn = document.getElementById('showReviews');
    const reviewContent = document.getElementById('reviewContent');
    let isReviewsVisible = false;

    // Đảm bảo ban đầu phần đánh giá được ẩn
    reviewContent.style.display = 'none';

    showReviewsBtn.addEventListener('click', function() {
        isReviewsVisible = !isReviewsVisible;
        
        if (isReviewsVisible) {
            reviewContent.style.display = 'block';
            showReviewsBtn.textContent = 'Ẩn đánh giá';
        } else {
            reviewContent.style.display = 'none';
            showReviewsBtn.textContent = 'Xem tất cả đánh giá';
        }
    });
});

document.getElementById('loadMoreReviews').addEventListener('click', function() {
    const button = this;
    const currentPage = parseInt(button.dataset.page);
    const totalReviews = parseInt(button.dataset.total);
    const perPage = 5;
    
    // Thêm class loading
    button.classList.add('loading');
    button.innerHTML = 'Đang tải... <i class="bi bi-arrow-repeat"></i>';
    
    // Gọi API để lấy thêm đánh giá
    fetch(`get-more-reviews.php?page=${currentPage + 1}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Thêm đánh giá mới vào danh sách
                const reviewsList = document.querySelector('.reviews-list');
                data.reviews.forEach(review => {
                    const reviewElement = createReviewElement(review);
                    reviewsList.appendChild(reviewElement);
                });
                
                // Cập nhật số trang hiện tại
                button.dataset.page = currentPage + 1;
                
                // Kiểm tra nếu đã tải hết đánh giá
                if ((currentPage + 1) * perPage >= totalReviews) {
                    button.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải thêm đánh giá');
        })
        .finally(() => {
            // Xóa class loading
            button.classList.remove('loading');
            button.innerHTML = 'Xem thêm đánh giá <i class="bi bi-chevron-down"></i>';
        });
});

// Hàm tạo element cho đánh giá
function createReviewElement(review) {
    const reviewDiv = document.createElement('div');
    reviewDiv.className = 'review-item';
    
    const stars = '★'.repeat(review.so_sao) + '☆'.repeat(5 - review.so_sao);
    
    reviewDiv.innerHTML = `
        <div class="review-header">
            <div class="user-info">
                <img src="avatars/${review.avatar}" alt="Avatar" class="review-avatar">
                <span class="username">${review.username}</span>
            </div>
            <div class="stars-display">
                ${stars.split('').map(star => `<span class="star ${star === '★' ? 'filled' : ''}">${star}</span>`).join('')}
            </div>
            <span class="date">${review.ngay_tao}</span>
        </div>
        <div class="review-content">
            ${review.noi_dung}
        </div>
    `;
    
    return reviewDiv;
}
</script>
<!-- Thêm vào trước </body> -->
<div class="notification-bell">
    <div class="bell-button" onclick="window.location.href='timkiem.php'">
        <i class="bi bi-bell-fill bell-icon"></i>
    </div>
</div>

<!-- Thêm style cho hover effect -->
<style>
.bell-button {
    width: 80px;
    height: 80px;
    background-color: #4CAF50;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.bell-button:hover {
    transform: scale(1.1);
    background-color: #45a049;
}

.bell-icon {
    color: white;
    font-size: 32px;
    animation: bellRing 2s infinite;
}

@keyframes bellRing {
    0% { transform: rotate(0); }
    10% { transform: rotate(15deg); }
    20% { transform: rotate(-15deg); }
    30% { transform: rotate(15deg); }
    40% { transform: rotate(-15deg); }
    50% { transform: rotate(0); }
    100% { transform: rotate(0); }
}
</style>
</body>
</html>
