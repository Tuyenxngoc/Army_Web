<?php
define('NP', true);
require(__DIR__ . '/../../core/configs.php');

header('Content-Type: application/json');

$post = json_decode(file_get_contents('php://input'), true);
$conn = SQL();

session_start();

try {
    if (!isset($_SESSION['user']['user'])) {
        echo json_encode(['code' => '01', 'text' => 'Thông tin tài khoản hoặc mật khẩu không chính xác.']);
        exit;
    }
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
    
    $username = $_SESSION['user']['user'];
    $stmt = $conn->prepare("SELECT * FROM user WHERE user = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_renew = $result->fetch_assoc();

    $sqlArmymem = 'SELECT online FROM armymem WHERE id = ' . $userId . ' LIMIT 1';
    $resultArmymem = SQL()->query($sqlArmymem);
    $armymemDB = $resultArmymem->fetch_assoc();
    $isOnline = $armymemDB['online'];

    if ($isOnline == 1) {
        echo '{"code": "99", "text": "Bạn chưa thoát game."}';
        return;
    }

    if (!$user_renew) {
        echo json_encode(['code' => '01', 'text' => 'Thông tin tài khoản hoặc mật khẩu không chính xác.']);
        exit;
    }

    if ($user_renew['balance'] < $fees['active']) {
        echo json_encode(['code' => '05', 'text' => 'Tài khoản không đủ số dư.']);
        exit;
    }

    $new_balance = $user_renew['balance'] - $fees['active'];
    $update_stmt = $conn->prepare("UPDATE user SET active = 1, balance = ? WHERE user = ?");
    $update_stmt->bind_param("is", $new_balance, $username);
    $charge_fee = $update_stmt->execute();

    if ($charge_fee) {
        echo json_encode(['code' => '00', 'text' => 'Kích hoạt tài khoản thành công.']);
    } else {
        echo json_encode(['code' => '06', 'text' => 'Kích hoạt tài khoản thất bại. Vui lòng liên hệ quản trị viên để được hỗ trợ.']);
    }
} catch (Exception $e) {
    echo json_encode(['code' => '99', 'text' => 'Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ.']);
}
?>
