<?php
require_once(__DIR__ . '/../../../core/configs.php');

$message = '';
$showAddPasswordForm = false;
$showChangePasswordForm = false;

if (isset($user['user'])) {
    $username = strip_tags($user['user']);
    $conn = SQL();
    try {
        $stmt = $conn->prepare("SELECT password2 FROM user WHERE user = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($stored_password2);
            $stmt->fetch();
            if (empty($stored_password2) || $stored_password2 === '-1') {
                $showAddPasswordForm = true;
            } else {
                $showChangePasswordForm = true;
            }
        } else {
            $message = '<div class="alert alert-danger" role="alert">Người dùng không tồn tại.</div>';
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $message = '<div class="alert alert-danger" role="alert">Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ.</div>';
    }
}

if (isset($_POST["submit"])) {
    $current_pass = isset($_POST['current_pass']) ? strip_tags($_POST['current_pass']) : '';
    $new_password = isset($_POST['new_password']) ? strip_tags($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? strip_tags($_POST['confirm_password']) : '';

    if ($new_password !== $confirm_password) {
        $message = '<div class="alert alert-danger" role="alert">Mật khẩu mới và mật khẩu xác nhận không khớp.</div>';
    } elseif (!preg_match('/^\d{6}$/', $new_password)) {
        $message = '<div class="alert alert-danger" role="alert">Mật khẩu cấp 2 phải chính xác 6 số.</div>';
    } else {
        try {
            if ($showChangePasswordForm) {
                $stmt = $conn->prepare("SELECT password2 FROM user WHERE user = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($stored_password2);
                    $stmt->fetch();
                    if ($current_pass === $stored_password2) {
                        $updateStmt = $conn->prepare("UPDATE `user` SET `password2` = ? WHERE `user` = ?");
                        $updateStmt->bind_param("ss", $new_password, $username);
                        $updateResult = $updateStmt->execute();

                        if ($updateResult) {
                            $_SESSION['message'] = '<div class="alert alert-success" role="alert">Thay đổi mật khẩu cấp 2 thành công.</div>';
                            header('Location: change-password-two');
                            exit;
                        } else {
                            $message = '<div class="alert alert-danger" role="alert">Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ.</div>';
                        }
                    } else {
                        $message = '<div class="alert alert-danger" role="alert">Mật khẩu hiện tại không đúng.</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger" role="alert">Người dùng không tồn tại.</div>';
                }
            } elseif ($showAddPasswordForm) {
                $updateStmt = $conn->prepare("UPDATE `user` SET `password2` = ? WHERE `user` = ?");
                $updateStmt->bind_param("ss", $new_password, $username);
                $updateResult = $updateStmt->execute();

                if ($updateResult) {
                    $_SESSION['message'] = '<div class="alert alert-success" role="alert">Thêm mật khẩu cấp 2 thành công.</div>';
                    header('Location: change-password-two');
                    exit;
                } else {
                    $message = '<div class="alert alert-danger" role="alert">Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ.</div>';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $message = '<div class="alert alert-danger" role="alert">Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ.</div>';
        }
    }
}
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); 
}
?>

<?php if ($showAddPasswordForm): ?>
<div class="w-100 d-flex justify-content-center">
    <form method="post" action="change-password-two" class="pb-3" style="width: 26rem;">
        <div class="fs-5 fw-bold text-center mb-3" style="color:#333 !important;">Thêm mật khẩu cấp 2</div>
        <?php if ($message) echo $message; ?>
        <div class="mb-2">
            <div class="input-group">
                <input name="new_password" type="password" autocomplete="off" placeholder="Mật khẩu cấp 2" class="form-control form-control-solid" value="">
                <div class="invalid-feedback">Không được bỏ trống</div>
            </div>
        </div>
        <div class="mb-2">
            <div class="input-group">
                <input name="confirm_password" type="password" autocomplete="off" placeholder="Nhập lại mật khẩu cấp 2" class="form-control form-control-solid" value="">
                <div class="invalid-feedback">Không được bỏ trống</div>
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="submit" class="me-3 btn btn-primary btn-success" name="submit">Thêm mật khẩu cấp 2</button>
        </div>
    </form>
</div>
<?php elseif ($showChangePasswordForm): ?>
<div class="w-100 d-flex justify-content-center">
    <form method="post" action="change-password-two" class="pb-3" style="width: 26rem;">
        <div class="fs-5 fw-bold text-center mb-3" style="color:#333 !important;">Đổi mật khẩu cấp 2</div>
        <?php if ($message) echo $message; ?>
        <div class="mb-2">
            <div class="input-group">
                <input name="current_pass" type="password" autocomplete="off" placeholder="Nhập mật khẩu cấp 2 hiện tại" class="form-control form-control-solid" value="">
                <div class="invalid-feedback">Không được bỏ trống</div>
            </div>
        </div>
        <div class="mb-2">
            <div class="input-group">
                <input name="new_password" type="password" autocomplete="off" placeholder="Mật khẩu cấp 2 mới" class="form-control form-control-solid" value="">
                <div class="invalid-feedback">Không được bỏ trống</div>
            </div>
        </div>
        <div class="mb-2">
            <div class="input-group">
                <input name="confirm_password" type="password" autocomplete="off" placeholder="Nhập lại mật khẩu cấp 2 mới" class="form-control form-control-solid" value="">
                <div class="invalid-feedback">Không được bỏ trống</div>
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="submit" class="me-3 btn btn-primary btn-success" name="submit">Đổi mật khẩu cấp 2</button>
        </div>
    </form>
</div>
<?php endif; ?>
