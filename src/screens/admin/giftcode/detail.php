<?php
require_once(__DIR__ . '/../../../../core/configs.php');

if (!isset($_SESSION['user'])) {
    header('Location: /home');
    exit;
}

$user = $_SESSION['user'];
if ($user['role'] != 1) {
    header("Location: /home");
    exit();
}

$conn = SQL();
$idgiftcode = intval($_GET['id']);
$giftcode = null;

$result = $conn->query("SELECT * FROM `giftcode` WHERE `id` = $idgiftcode");
if ($result) {
    $giftcode = $result->fetch_assoc();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $giftcode_value = $_POST['giftcode'] ?? '';    
    $giftlimit = $_POST['giftlimit'] ?? '';    
    $giftiddblist = $_POST['giftiddblist'] ?? '';    
    $giftpublic = $_POST['giftpublic'] ?? '';    
    $gifttime = $_POST['giftexpire'] ?? '';    
    $giftitem = $_POST['giftitem'] ?? '';    
    $stmt = $conn->prepare("UPDATE `giftcode` SET `code` = ?, `limit` = ?, `iddblist` = ?, `public` = ?, `expire` = ?, `item` = ? WHERE `id` = ?");
    $stmt->bind_param("ssssssi", $giftcode_value, $giftlimit, $giftiddblist, $giftpublic, $gifttime, $giftitem, $idgiftcode);

    if ($stmt->execute()) {
        $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Cập nhật thành công.</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Cập nhật thất bại. Lỗi: ' . $stmt->error . '</div>';
    }
    
    $stmt->close();
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

if (isset($_SESSION['update_message'])) {
    echo $_SESSION['update_message'];
    unset($_SESSION['update_message']); 
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Mã Quà Tặng</title>
</head>
<body>
    <div class="box-nf" id="box-nf"></div>
    <script type="text/javascript" src="../JavaScript/box.js"></script>
    <script language="javascript" src="../JavaScript/jquery-2.0.0.min.js"></script>
    <div class="bg-content" style="background: rgb(4, 58, 52); border-radius: 1rem; padding:10px">
        <div style="text-align:center;">
            <h4><?php echo htmlspecialchars($user['user']); ?> đang sửa mã quà tặng</h4>
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
        <h4>Chỉnh Sửa</h4>
    </div>
    <?php if ($message) echo '<p>' . htmlspecialchars($message) . '</p>'; ?>
    <div class="mt-2">
        <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
            <form method="POST" action="">
                <table class="table text-white fw-semibold mb-0" role="table" width="100%">
                    <tbody class="fw-semibold" role="rowgroup">
                        <tr role="row">
                            <td role="cell">Mã Quà Tặng:</td>
                            <td role="cell"><input type="text" class="box-text" id="giftcode" name="giftcode" placeholder="Tên mã quà tặng" value="<?php echo htmlspecialchars($giftcode['code']); ?>" /></td>
                        </tr>
                        <tr role="row">
                            <td>Giới hạn:</td>
                            <td><input type="text" class="box-text" id="limit" name="giftlimit" value="<?php echo htmlspecialchars($giftcode['limit']); ?>" placeholder="Giới hạn số lần nhập" /></td>
                        </tr>
                        <tr role="row">
                            <td>Danh sách iddb:</td>
                            <td><textarea class="box-text" id="iddblist" name="giftiddblist" placeholder="Danh sách thành viên nhập mã quà tặng"><?php echo htmlspecialchars($giftcode['iddblist']); ?></textarea></td>
                        </tr>
                        <tr role="row">
                            <td>Lượt nhập:</td>
                            <td><input type="text" class="box-text" id="public" name="giftpublic" value="<?php echo htmlspecialchars($giftcode['public']); ?>" /></td>
                        </tr>
                        <tr role="row">
                            <td>Thời gian hết:</td>
                            <td><input type="text" class="box-text" id="expire" name="giftexpire" value="<?php echo htmlspecialchars($giftcode['expire']); ?>" placeholder="Ngày tạo mã quà tặng" /></td>
                        </tr>
                        <tr role="row">
                            <td>Vật phẩm:</td>
                            <td>
                                <textarea class="box-text" id="item" name="giftitem" placeholder="Vật phẩm"><?php echo $giftcode['Item']; ?></textarea>
                            </td>
                        </tr>
                        <tr role="row">
                            <td colspan="2">
                                <input type="hidden" name="code_id" value="<?php echo htmlspecialchars($idgiftcode); ?>" />
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
    input[type="password"],
    input[type="email"] {
        width: 100%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
        margin-bottom: 10px;
    }
    textarea {
        width: 100%; 
        height: 500px !important;
        padding: 10px;
        border: 1px solid #ccc; 
        border-radius: 4px;
        box-sizing: border-box; 
        font-size: 16px;
        margin-bottom: 10px;
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
