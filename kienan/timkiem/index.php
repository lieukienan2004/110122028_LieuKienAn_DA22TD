<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm khách sạn</title>
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .search-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .nav-tabs {
            display: flex;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }

        .nav-item {
            padding: 10px 20px;
            cursor: pointer;
            position: relative;
            color: #666;
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
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-group i {
            color: #666;
            font-size: 20px;
        }

        .input-content {
            flex: 1;
        }

        .input-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 4px;
        }

        .input-value {
            font-size: 16px;
            color: #333;
        }

        .search-btn {
            background-color: #f39c12;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <div class="close-btn">&times;</div>
        
        <h2>Tìm kiếm khách sạn</h2>

        <div class="nav-tabs">
            <div class="nav-item active">Khách sạn</div>
            <div class="nav-item">Vé MB & Khách sạn</div>
            <div class="nav-item">Tour & Trải nghiệm</div>
            <div class="nav-item">Staynfun</div>
        </div>

        <form class="search-form">
            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <div class="input-content">
                    <div class="input-label">Chọn điểm đến, khách sạn theo sở thích ...</div>
                    <div class="input-value">Vinpearl Wonderworld Phú Quốc</div>
                </div>
            </div>

            <div class="input-group">
                <i class="far fa-calendar"></i>
                <div class="input-content">
                    <div class="input-label">Ngày nhận - Trả phòng</div>
                    <div class="input-value">31/12/2024 - 02/01/2025</div>
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-user"></i>
                <div class="input-content">
                    <div class="input-label">Số phòng - Số người</div>
                    <div class="input-value">1 Phòng - 1 Người lớn - 0 Trẻ em - 0 Em bé</div>
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-tag"></i>
                <div class="input-content">
                    <div class="input-label">Mã ưu đãi</div>
                    <input type="text" placeholder="Nhập mã ưu đãi" style="border: none; outline: none; width: 100%; font-size: 16px;">
                </div>
            </div>

            <button type="submit" class="search-btn">Tìm kiếm</button>
        </form>
    </div>

    <script>
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.nav-item').forEach(tab => {
                    tab.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        document.querySelector('.close-btn').addEventListener('click', function() {
            console.log('Đóng form tìm kiếm');
        });
    </script>
</body>
</html> 