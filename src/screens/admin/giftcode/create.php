<?php
require_once(__DIR__ . '/../../../../core/configs.php');

if (!isset($_SESSION['user'])) {
    header('Location: /home');
    exit;
}

$message = '';
$user = $_SESSION['user'];

if ($user['role'] != 1) {
    header("Location: /home");
    exit();
}

$conn = SQL();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $giftcode = $_POST['giftcode'] ?? '';
    $giftlimit = $_POST['giftlimit'] ?? '';
    $giftpublic = $_POST['giftpublic'] ?? '';
    $gifttime = $_POST['giftexpire'] ?? '';
    $giftitem = $_POST['giftitem'] ?? '';
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `giftcode` WHERE `code` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $giftcode);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $message = '<div class="alert alert-danger" role="alert">Mã quà tặng đã tồn tại. Vui lòng tạo mã khác.</div>';
        } else {
            $stmt = $conn->prepare("INSERT INTO `giftcode` (`code`, `limit`, `public`, `expire`, `Item`) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $giftcode, $giftlimit, $giftpublic, $gifttime, $giftitem);

                if ($stmt->execute()) {
                    $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Thêm mã quà tặng thành công.</div>';
                    header("Location: /admin/create"); 
                    exit();
                } else {
                    $message = '<div class="alert alert-danger" role="alert">Thêm mã quà tặng thất bại. Lỗi: ' . htmlspecialchars($stmt->error) . '</div>';
                }

                $stmt->close();
            } else {
                $message = '<div class="alert alert-danger" role="alert">Lỗi chuẩn bị câu lệnh SQL.</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger" role="alert">Lỗi chuẩn bị câu lệnh SQL kiểm tra mã.</div>';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Mới Mã Quà Tặng</title>
    <link rel="stylesheet" href="../path/to/your/styles.css"> <!-- Include your CSS file -->
</head>
<body>
    <div class="box-nf" id="box-nf"></div>
    <script type="text/javascript" src="../JavaScript/box.js"></script>
    <script src="../JavaScript/jquery-2.0.0.min.js"></script>
    <div class="bg-content" style="background: rgb(4, 58, 52); border-radius: 1rem; padding:10px">
        <div style="text-align:center;">
            <h4><?php echo htmlspecialchars($user['user']); ?> tạo mã quà tặng</h4>
        </div>
        <div class="container mb-2">
            <div class="row text-center justify-content-center g-2 mt-1">
                <div class="col-12 col-md-4 col-lg-3">
                    <a class="btn btn-success w-100 fw-semibold" href="/admin/code">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
    <div class="title">
        <h4>Thêm Mới</h4>
    </div>
    <?php if ($message) echo $message; ?>
    <div class="mt-2">
        <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
            <form method="POST" action="">
                <table class="table text-white fw-semibold mb-0" role="table" width="100%">
                    <tbody class="fw-semibold" role="rowgroup">
                        <tr role="row">
                            <td role="cell">Mã Quà Tặng:</td>
                            <td role="cell"><input type="text" class="box-text" id="giftcode" name="giftcode" placeholder="Tên mã quà tặng" required /></td>
                        </tr>
                        <tr role="row">
                            <td>Giới hạn:</td>
                            <td><input type="number" class="box-text" id="limit" name="giftlimit" value="-1" placeholder="Giới hạn số lần nhập" required /></td>
                        </tr>
                        <tr role="row">
                            <td>Lượt nhập:</td>
                            <td><input type="number" class="box-text" id="public" name="giftpublic" value="1" required /></td>
                        </tr>
                        <tr role="row">
                            <td>Thời gian hết:</td>
                            <td><input type="datetime-local" class="box-text" id="expire" name="giftexpire" value="2024-01-01T00:00" required /></td>
                        </tr>
                        <tr role="row">
                            <td>Vật phẩm:</td>
                            <td>
                                <textarea class="box-text" id="item" name="giftitem" placeholder="Vật phẩm" required>{"xu":0,"xu_khoa":0,"luong":1000,"exp":0,"cup":0,"item":[],"equip":[]}</textarea>
                            </td>
                        </tr>
                        <tr role="row">
                            <td colspan="2">
                                <button class="btn btn-success w-100 fw-semibold" type="submit">Cập nhật</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</body>
</html>

<style>
    input[type="text"],
    input[type="number"],
    input[type="datetime-local"],
    textarea {
        width: 100%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
        margin-bottom: 10px;
    }

    textarea {
        height: 200px;
        resize: vertical;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .label {
        display: block; 
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 16px;
    }
</style>
