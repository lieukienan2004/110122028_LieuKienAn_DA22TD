<?php
session_start();
require_once 'config/database.php';

// Tạo thư mục avatars nếu chưa tồn tại
$uploaddir = __DIR__ . '/avatars/';
if (!file_exists($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}

// Tạo avatar mặc định
$defaultAvatar = $uploaddir . 'default-avatar.jpg';
if (!file_exists($defaultAvatar)) {
    // URL của ảnh avatar mặc định
    $defaultImageUrl = 'https://www.gravatar.com/avatar/default?s=200&d=mp';
    
    // Tải và lưu ảnh
    $imageContent = file_get_contents($defaultImageUrl);
    if ($imageContent !== false) {
        file_put_contents($defaultAvatar, $imageContent);
    } else {
        die('Không thể tạo avatar mặc định');
    }
}

// Thêm vào đầu file để debug
if (!is_writable($uploaddir)) {
    die('Thư mục avatars không có quyền ghi. Vui lòng kiểm tra lại quyền truy cập.');
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit();
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Lấy thông tin hiện tại của user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Xử lý upload avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $newname = uniqid() . '.' . $filetype;
            $uploaddir = __DIR__ . '/avatars/';
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploaddir . $newname)) {
                // Xóa avatar cũ nếu không phải avatar mặc định
                if ($user['avatar'] != 'default-avatar.jpg' && file_exists($uploaddir . $user['avatar'])) {
                    unlink($uploaddir . $user['avatar']);
                }
                
                // Cập nhật tên file avatar mới trong database
                $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$newname, $userId]);
                $_SESSION['avatar'] = $newname;
            }
        }
    }

    try {
        // Kiểm tra mật khẩu hiện tại
        if (!empty($currentPassword)) {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $currentHash = $stmt->fetchColumn();

            if (!password_verify($currentPassword, $currentHash)) {
                $error = "Mật khẩu hiện tại không đúng";
            } else if ($newPassword !== $confirmPassword) {
                $error = "Mật khẩu mới không khớp";
            } else {
                // Cập nhật thông tin và mật khẩu mới
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$username, $email, $newHash, $userId]);
            }
        } else {
            // Chỉ cập nhật thông tin, không đổi mật khẩu
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $userId]);
        }

        $_SESSION['username'] = $username;
        $success = "Cập nhật thông tin thành công!";
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
    } catch(PDOException $e) {
        $error = "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thay Đổi Thông Tin</title>
    <link rel="icon" type="image/x-icon" href="images/kienan.jpg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .avatar-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #4CAF50;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            color: green;
            margin-bottom: 20px;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .password-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .password-section h3 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thay Đổi Thông Tin</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="avatar-container">
                <img src="avatars/<?php echo htmlspecialchars($user['avatar']); ?>" 
                     alt="Avatar" 
                     class="avatar-preview" 
                     id="avatarPreview">
                <input type="file" 
                       name="avatar" 
                       id="avatarInput" 
                       accept="image/*" 
                       style="display: none;">
                <button type="button" 
                        class="btn" 
                        style="width: auto;" 
                        onclick="document.getElementById('avatarInput').click()">
                    Thay đổi ảnh đại diện
                </button>
            </div>

            <div class="form-group">
                <label for="username">Tên người dùng:</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                       required>
            </div>

            <div class="password-section">
                <h3>Đổi mật khẩu</h3>
                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại:</label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">Mật khẩu mới:</label>
                    <input type="password" 
                           id="new_password" 
                           name="new_password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password">
                </div>
            </div>

            <button type="submit" class="btn">Cập nhật thông tin</button>
        </form>

        <a href="index.php" class="back-link">Quay lại trang chủ</a>
    </div>

    <script>
        // Preview ảnh đại diện khi chọn file
        document.getElementById('avatarInput').onchange = function(e) {
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        }
    </script>
</body>
</html> 