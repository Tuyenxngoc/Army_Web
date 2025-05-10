<?php

if (!isset($_SESSION['user'])) {
    header('Location: /home');
    exit;
}
$user = $_SESSION['user'];
$user_id = isset($user['user_id']) ? (int)$user['user_id'] : 1;
$conn = SQL();
$stmt = $conn->prepare("SELECT * FROM `armymem` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$armymem = $stmt->get_result()->fetch_assoc();
$stmt->close();

$message = '';
if ($armymem['request_clan'] > 0 || $armymem['clan'] > 0) {
    $message = '
    <div class="bg-content" style="background: rgb(4, 58, 52); border-radius: 1rem; padding:10px">
        <div style="text-align:center;">
            <h4>Tài khoản đã là thành viên của đội khác</h4>
        </div>
    </div>
    ';
} elseif ($armymem['online'] > 0) {
    $message = '<div class="alert alert-danger" role="alert">Lỗi: Tài khoản phải thoát game để đăng ký.</div>';
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clan_name = trim($_POST['clan-name']);
    $clan_desc = trim($_POST['clan-desc']);
    $clan_phone = trim($_POST['clan-phone']);
    $clan_email = trim($_POST['clan-email']);
    $clan_icon = trim($_POST['clan-icon']);
    $time = date("Y-m-d H:i:s");
    if (empty($clan_name) || empty($clan_desc) || empty($clan_phone) || empty($clan_email) || empty($clan_icon)) {
        $message = '<div class="alert alert-danger" role="alert">Lỗi: Vui lòng điền đầy đủ thông tin.</div>';
    } elseif (strlen($clan_name) < 5 || strlen($clan_name) > 15) {
        $message = '<div class="alert alert-danger" role="alert">Lỗi: Tên đội phải có từ 5 đến 15 ký tự.</div>';
    } elseif (!preg_match('/^[0-9]+$/', $clan_phone) || !preg_match('/^[0-9]+$/', $clan_icon)) {
        $message = '<div class="alert alert-danger" role="alert">Lỗi: Có ký tự không hợp lệ, vui lòng kiểm tra lại.</div>';
    } elseif ($conn->query("SELECT `id` FROM `clan` WHERE `name` = '" . $conn->real_escape_string($clan_name) . "'")->num_rows > 0) {
        $message = '<div class="alert alert-danger" role="alert">Lỗi: Tên đội đã tồn tại.</div>';
    } elseif ($armymem['luong'] < 50000) {
        $message = '<div class="alert alert-danger" role="alert">Lỗi: Tài khoản cần có ít nhất 50,000 lượng để tạo đội.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO `clan` (`name`, `master`, `masterName`, `desc`, `phone`, `email`, `icon`, `dateCreat`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissssss", $clan_name, $user['user_id'], $user['user'], $clan_desc, $clan_phone, $clan_email, $clan_icon, $time);
        $stmt->execute();

        $clan_id = $stmt->insert_id;
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO `clanmem` (`clan`, `user`, `user_name`, `timejoin`, `rights`)
            VALUES (?, ?, ?, ?, 2)");
        $stmt->bind_param("iiss", $clan_id, $user['user_id'], $user['user'], $time);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE `armymem` SET `luong` = `luong` - 50000, `clan` = ?, `request_clan` = ? WHERE `id` = ?");
        $stmt->bind_param("iii", $clan_id, $clan_id, $armymem['id']);
        $stmt->execute();
        $stmt->close();
        $message = '<div class="alert alert-success" role="alert">Tạo đội thành công. <a style="color: blue;" href="/squad/info">Xem đội của bạn tại đây.</a></div>';
    }
}

?>
<?php if (!$armymem['request_clan'] && !$armymem['clan']): ?>
<div class="bg-content" style="background: rgb(4, 58, 52); border-radius: 1rem; padding:10px">
    <div style="text-align:center;">
        <h4>Đăng ký thành lập đội</h4>
        <h5>Lưu ý: Cần điền thông tin chính xác để lấy lại mật khẩu.</h5>
    </div>
    <div class="container mb-2">
        <div class="row text-center justify-content-center g-2 mt-1">
            <div class="col-12 col-md-4 col-lg-3">
                <a class="btn btn-success w-100 fw-semibold" href="/squad">Quay lại</a>
            </div>
        </div>
    </div>
</div>
<br>
<?php if ($message) echo $message; ?>
<div class="mt-2">
    <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
        <form method="POST" action="">
            <table class="table text-white fw-semibold mb-0" role="table" width="100%">
                <tbody class="fw-semibold" role="rowgroup">
                    <tr role="row">
                        <td>Tên đội (*):</td>
                        <td><input type="text" name="clan-name" required></td>
                    </tr>
                    <tr role="row">
                        <td>Giới thiệu:</td>
                        <td><textarea name="clan-desc" rows="2" cols="40"></textarea></td>
                    </tr>
                    <tr role="row">
                        <td>Số điện thoại (*):</td>
                        <td><input type="tel" name="clan-phone" size="14" required></td>
                    </tr>
                    <tr role="row">
                        <td>Email (*) (Ví dụ: abc@gmail.com):</td>
                        <td><input type="email" name="clan-email" size="20" required></td>
                    </tr>
                    <tr role="row">
                        <td colspan="2">Hãy lựa chọn ảnh đại diện cho đội:<br>
                            <table width="100%">
                                <?php
                                $ids = 0;
                                for ($i = 1; $i <= 20; $i++) {
                                    if ($ids > 188) break;
                                ?>
                                    <tr>
                                        <?php
                                        for ($j = 1; $j <= 10; $j++) {
                                            $ids += 1;
                                            if ($ids > 188) break;
                                        ?>
                                            <td style="border: 1px solid #AAA;">
                                                <img src="/images/res/icon/<?php echo htmlspecialchars($ids); ?>.png" alt="Icon <?php echo htmlspecialchars($ids); ?>">
                                                <input type="radio" name="clan-icon" value="<?php echo htmlspecialchars($ids); ?>" required>&nbsp;&nbsp;&nbsp;
                                            </td>
                                        <?php
                                        }
                                        ?>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                    <tr role="row">
                        <td colspan="2">
                            <button class="btn btn-success w-100 fw-semibold" type="submit">Xác nhận</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php endif; ?>
</div>

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
        height: 100px !important;
        padding: 10px;
        border: 1px solid #ccc; 
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
        margin-bottom: 10px;
    }

    button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        background-color: #28a745;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #218838;
    }
</style>
