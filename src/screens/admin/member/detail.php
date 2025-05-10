<?php
require_once(__DIR__ . '/../../../../core/configs.php');
if (!isset($_SESSION['user'])) {
    header('Location: /home');
    exit;
}
$user = ['role' => 1];
if ($user['role'] != 1) {
    header("Location: /home");
    exit();
}
$message = '';
$user = $_SESSION['user'];
$conn = SQL();
$user_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM `user` WHERE `user_id` = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$nameNV = array('Guner', 'Miss 6', 'Proton', 'King Kong', 'Rocket', 'Cano', 'Chicky', 'Tarzan', 'Apache', 'Magenta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $uname = $_POST['user'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $lock = $_POST['lock'] ?? '';
    $email = $_POST['email'] ?? '';
    $active = $_POST['active'] ?? '';
    $CSinh = $_POST['CSinh'] ?? '';
    $NVCSinh = $_POST['NVCSinh'] ?? '';
    $xu = $_POST['xu'] ?? '';
    $xu_khoa = $_POST['xukhoa'] ?? '';
    $luong = $_POST['luong'] ?? '';
    $XPMax = $_POST['XPMax'] ?? '';
    $dvong = $_POST['dvong'] ?? '';
    $vip_level = $_POST['vip_level'] ?? '';
    $item = $_POST['item'] ?? '';
    $ruongItem = $_POST['ruongItem'] ?? '';
    $ruongTB = $_POST['ruongTB'] ?? '';
    $NV = [];
    for ($i = 0; $i < 10; $i++) {
        $NV[($i + 1)] = $_POST['NV' . ($i + 1)] ?? '';
    }

    $updateQuery = "UPDATE `armymem` SET
                    vip_level = ?,
                    CSinh = ?,
                    NVCSinh = ?,
                    xu = ?,
                    xu_khoa = ?,
                    luong = ?,
                    xpMax = ?,
                    dvong = ?,
                    item = ?,
                    ruongItem = ?,
                    ruongTrangBi = ?";
    for ($i = 0; $i < 10; $i++) {
        $updateQuery .= ", NV" . ($i + 1) . " = ?";
    }
    $updateQuery .= " WHERE id = ?";
    
    $params = [
        $vip_level,
        $CSinh,
        $NVCSinh,
        $xu,
        $xu_khoa,
        $luong,
        $XPMax,
        $dvong,
        $item,
        $ruongItem,
        $ruongTB
    ];
    for ($i = 0; $i < 10; $i++) {
        $params[] = $_POST['NV' . ($i + 1)];
    }
    $params[] = $user_id;

    $stmt = $conn->prepare($updateQuery);
    $types = str_repeat('s', count($params) - 1) . 'i';
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Cập nhật thành công.</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Cập nhật thất bại. Lỗi: ' . $stmt->error . '</div>';
    }
    $stmt->close();
    
    $stmt = $conn->prepare("UPDATE `user` SET 
                            `user` = ?, 
                            `password` = ?, 
                            `password2` = ?, 
                            `lock` = ?, 
                            `email` = ?, 
                            `active` = ? 
                            WHERE `user_id` = ?");
    $stmt->bind_param("ssssssi", $uname, $password, $password2, $lock, $email, $active, $user_id);
    
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
?>

<body>
    <div class="box-nf" id="box-nf"></div>
    <script type="text/javascript" src="../JavaScript/box.js"></script>
    <script language="javascript" src="../JavaScript/jquery-2.0.0.min.js"></script>
    <div class="bg-content" style="background: rgb(4, 58, 52); border-radius: 1rem; padding:10px">
        <div style="text-align:center;">
            <h4>Đang chỉnh sửa thành viên <?php echo htmlspecialchars($user['user']); ?></h4>
        </div>

        <div class="container mb-2">
            <div class="row text-center justify-content-center g-2 mt-1">
                <div class="col-12 col-md-4 col-lg-3">
                    <a class="btn btn-success w-100 fw-semibold" href="/admin/member">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
    <div class="title">
        <h4>Chỉnh Sửa</h4>
    </div>
    <?php if ($message) echo $message; ?>
    <div class="mt-2">
        <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
            <form method="POST" action="">
                <?php
                if ($result->num_rows == 0) {
                    echo '<p>Tài khoản không tồn tại</p>';
                } else {
                    $result = $conn->query("SELECT * FROM `armymem` WHERE `id` = " . $user['user_id'] . "; ");
                    if ($result->num_rows == 0) {
                        echo '<p>Tài khoản chưa kích hoạt</p>';
                    } else {
                        $army = $result->fetch_assoc();
                        if ($army['online'] != 0) {
                            echo '<p style="color:green">Tài khoản có đăng nhập nếu sửa chữa dữ liệu có thể không có tác dụng</p>';
                        }
                    }
                ?>
                    <table class="table text-white fw-semibold mb-0" role="table" width="100%">
                        <tbody class="fw-semibold" role="rowgroup">
                            <tr role="row">
                                <td role="cell">Tài khoản:</td>
                                <td role="cell"><input type="text" class="box-text" id="user" name="user" placeholder="Tên tài khoản" value="<?php echo htmlspecialchars($user['user']); ?>" /></td>
                            </tr>
                            <tr role="row">
                                <td>Mật khẩu:</td>
                                <td><input type="text" class="box-text" id="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" placeholder="Mật khẩu" /></td>
                            </tr>
                            <tr role="row">
                                <td>Mật khẩu 2:</td>
                                <td><input type="text" class="box-text" id="password2" placeholder="Chữ ký" value="<?php echo htmlspecialchars($user['password2']); ?>" name="password2" /></td>
                            </tr>
                            <tr role="row">
                                <td>Email:</td>
                                <td><input type="text" class="box-text" id="email" placeholder="E-mail" value="<?php echo htmlspecialchars($user['email']); ?>" name="email" /></td>
                            </tr>
                            <tr role="row">
                                <td>Kích Hoạt:</td>
                                <td><input type="text" class="box-text" id="active" placeholder="Kích hoạt" value="<?php echo htmlspecialchars($user['active']); ?>" name="active" /></td>
                            </tr>
                            <tr role="row">
                                <td>VIP:</td>
                                <td><input type="text" class="box-text" id="vip_level" placeholder="VIP" value="<?php echo htmlspecialchars($army['vip_level']); ?>" name="vip_level" /></td>
                            </tr>
                            <tr role="row">
                                <td>Khoá tài khoản:</td>
                                <td><input type="text" class="box-text" id="lock" placeholder="Khoá tài khoản" value="<?php echo htmlspecialchars($user['lock']); ?>" name="lock" /></td>
                            </tr>
                            <tr role="row">
                                <td>Ngày sinh:</td>
                                <td><input type="text" class="box-text" id="CSinh" placeholder="Ngày sinh" value="<?php echo htmlspecialchars($army['CSinh']); ?>" name="CSinh" /></td>
                            </tr>
                            <tr role="row">
                                <td>NVCSinh:</td>
                                <td><input type="text" class="box-text" id="NVCSinh" placeholder="NVCSinh" value="<?php echo htmlspecialchars($army['NVCSinh']); ?>" name="NVCSinh" /></td>
                            </tr>
                            <tr role="row">
                                <td>Xu:</td>
                                <td><input type="text" class="box-text" id="xu" placeholder="Xu" value="<?php echo htmlspecialchars($army['xu']); ?>" name="xu" /></td>
                            </tr>
                            <tr role="row">
                                <td>Xu khoá:</td>
                                <td>
                                    <input type="text" class="box-text" id="xukhoa" placeholder="Xu khoá" value="<?php echo htmlspecialchars($army['xu_khoa']); ?>" name="xukhoa" />
                                </td>
                            </tr>
                            <tr role="row">
                                <td>Lương:</td>
                                <td><input type="text" class="box-text" id="luong" placeholder="Lương" value="<?php echo htmlspecialchars($army['luong']); ?>" name="luong" /></td>
                            </tr>
                            <tr role="row">
                                <td>XP Max:</td>
                                <td><input type="text" class="box-text" id="XPMax" placeholder="XP Max" value="<?php echo htmlspecialchars($army['xpMax']); ?>" name="XPMax" /></td>
                            </tr>
                            <tr role="row">
                                <td>DVong:</td>
                                <td><input type="text" class="box-text" id="dvong" placeholder="DVong" value="<?php echo htmlspecialchars($army['dvong']); ?>" name="dvong" /></td>
                            </tr>
                            <tr role="row">
                                <td>Item:</td>
                                <td><input type="text" class="box-text" id="item" placeholder="Item" value="<?php echo htmlspecialchars($army['item']); ?>" name="item" /></td>
                            </tr>
                            <tr role="row">
                                <td>Ruong Item:</td>
                                <td><input type="text" class="box-text" id="ruongItem" placeholder="Ruong Item" value="<?php echo htmlspecialchars($army['ruongItem']); ?>" name="ruongItem" /></td>
                            </tr>
                            <tr role="row">
                                <td>Rương trang bị:</td>
                                <td><textarea class="box-text" id="ruongTB" placeholder="Rương trang bị" name="ruongTB"><?php echo $army['ruongTrangBi']; ?></textarea></td>
                            </tr>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <tr role="row">
                                    <td>NV <?php echo $i; ?>:</td>
                                    <td><input type="text" class="box-text" id="NV<?php echo $i; ?>" placeholder="NV <?php echo $i; ?>" value="<?php echo htmlspecialchars($army["NV$i"]); ?>" name="NV<?php echo $i; ?>" /></td>
                                </tr>
                            <?php endfor; ?>
                            <tr role="row">
                                <td colspan="2">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" />
                                    <button class="btn btn-success w-100 fw-semibold" type="submit" class="btn btn-primary">Cập nhật</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
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