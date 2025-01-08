<?php
// Kiểm tra kết nối database
require_once 'config/db.php';

// Kiểm tra có ID được truyền vào không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Lấy ID từ URL
$id = $_GET['id'];

// Query lấy thông tin địa điểm
$sql = "SELECT * FROM dia_diem WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$diaDiem = $stmt->fetch();

// Kiểm tra nếu không tìm thấy địa điểm
if (!$diaDiem) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/kienan.jpg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
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
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
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
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            margin: 10px;
            width: 250px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .product:hover {
            transform: translateY(-5px);
        }
        .product img {
            width: 100%;
            height: auto;
            border-radius: 5px;
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
.product-detail {
   background-color: white;
   border: 1px solid #eaeaea;
   border-radius: 12px;
   padding: 30px;
   margin: 40px auto;
   max-width: 1000px;
   box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}
.product-detail img {
   max-width: 100%;
   height: auto;
   border-radius: 12px;
   margin-bottom: 30px;
   box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
   transition: transform 0.3s ease;
}
.product-detail img:hover {
   transform: scale(1.02);
}
.product-detail h1 {
   color: #2c3e50;
   margin-bottom: 20px;
   font-size: 2.5em;
   font-weight: 700;
   text-align: center;
}
.product-detail .category {
   color: #7f8c8d;
   font-style: italic;
   margin-bottom: 25px;
   text-align: center;
   font-size: 1.1em;
}
.recipe-details {
   display: grid;
   grid-template-columns: 1fr 2fr;
   gap: 30px;
   margin: 30px 0;
}
.price {
   background-color: #f8f9fa;
   padding: 20px;
   border-radius: 8px;
   text-align: center;
}
.price h3 {
   color: #2c3e50;
   margin-bottom: 10px;
}
.price p {
   color: #e74c3c;
   font-size: 1.5em;
   font-weight: bold;
}
.details {
   background-color: #f8f9fa;
   padding: 25px;
   border-radius: 8px;
}
.details h3 {
   color: #2c3e50;
   margin-bottom: 15px;
   border-bottom: 2px solid #4CAF50;
   padding-bottom: 10px;
}
.back-button {
   display: inline-block;
   padding: 12px 25px;
   background-color: #4CAF50;
   color: white;
   text-decoration: none;
   border-radius: 8px;
   margin-top: 30px;
   transition: all 0.3s ease;
   font-weight: bold;
}
.back-button:hover {
   background-color: #45a049;
   transform: translateY(-2px);
   box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
}
/* Thêm responsive design */
media (max-width: 768px) {
   .recipe-details {
       grid-template-columns: 1fr;
   }
}
   .product-detail {
       margin: 20px;
       padding: 20px;
   }
   
   .product-detail h1 {
       font-size: 2em;
   }

.map-link {
    display: inline-block;
    margin-top: 10px;
    color: #4CAF50;
    text-decoration: none;
    font-size: 0.9em;
}

.map-link:hover {   
    color: #45a049;
    text-decoration: underline;
}

.bi-geo-alt-fill {
    margin-right: 5px;
}

    </style>
</head>
<body>
<div class="container">
  <?php if ($diaDiem): ?>
   <div class="product-detail">
  <?php if (!empty($diaDiem['hinh_anh'])): ?>
     <img src="images/<?php echo htmlspecialchars($diaDiem['hinh_anh']); ?>" 
          alt="<?php echo htmlspecialchars($diaDiem['ten']); ?>">
  <?php endif; ?>
  
  <h1><?php echo htmlspecialchars($diaDiem['ten']); ?></h1>
  
  <div class="recipe-details">
 <div class="price">
  <h3>Địa chỉ:</h3>
  <p>
    <?php echo htmlspecialchars($diaDiem['dia_chi']); ?>
    <br>
    <?php if (!empty($diaDiem['map_url'])): ?>
      <a href="<?php echo htmlspecialchars($diaDiem['map_url']); ?>" 
         target="_blank" 
         class="map-link">
         <i class="bi bi-geo-alt-fill"></i> Xem trên Google Maps
      </a>
    <?php else: ?>
      <a href="https://www.google.com/maps/search/<?php echo urlencode($diaDiem['dia_chi']); ?>" 
         target="_blank" 
         class="map-link">
         <i class="bi bi-geo-alt-fill"></i> Xem trên Google Maps
      </a>
    <?php endif; ?>
  </p>
 </div> 
  
  <a href="index.php" class="back-button">Quay lại</a>
   </div>
  <?php else: ?>
      <p>Không tìm thấy thông tin địa điểm</p>
  <?php endif; ?>
    </div>
</body>
</html>
