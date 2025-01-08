<?php
$image_dir = __DIR__ . '/images/';
$images = ['nuocleobabien.jpg', 'bunnuocleo1.jpg', 'bunnuocleo2.jpg'];

echo "Kiểm tra thư mục ảnh:<br>";
echo "Đường dẫn thư mục: " . $image_dir . "<br><br>";

foreach ($images as $image) {
    $full_path = $image_dir . $image;
    if (file_exists($full_path)) {
        echo "✅ File '$image' tồn tại<br>";
        echo "Kích thước: " . filesize($full_path) . " bytes<br>";
        echo "Quyền truy cập: " . substr(sprintf('%o', fileperms($full_path)), -4) . "<br><br>";
    } else {
        echo "❌ File '$image' không tồn tại<br><br>";
    }
}
?> 