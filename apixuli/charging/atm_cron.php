<?php

define('NP', true);
require(__DIR__ . '/../../core/configs.php');
$post = json_decode(file_get_contents('php://input'), true);
$choose = $post['pcoin'];
try {
    session_start();
    $user = $_SESSION['user'];
    $sqlUs = 'SELECT user FROM user WHERE user_id=' . $user['user_id'] . ' LIMIT 1';
    $resultUs = SQL()->query($sqlUs);
    $userDB = $resultUs->fetch_assoc();
    $username = $userDB['user'];
    if(!isset($list_recharge_price_atm[$choose])) {
        echo '{"code": "01", "text": "Bạn chưa chọn số tiền nạp"}';
    }
    if(isset($list_recharge_price_atm[$choose]) && isset($configNapTien['atm']['sotaikhoan'])){
        $amount = $list_recharge_price_atm[$choose]['amount'];
        $acctNum = $configNapTien['atm']['sotaikhoan'];
        $namebank = $configNapTien['atm']['chutaikhoan'];
        $nhbank = $configNapTien['atm']['nganhang'];

        $qr = getQrAtmPayment($username, $amount, $acctNum);
        $gia = getgiaAtmPayment($amount);

        echo '{"code": "00", "text": "Lấy thông tin thanh toán thành công.", "qr_atm": "'.$qr.'", "gia_pay": "'.$gia.'", "name_pay": "'.$namebank.'", "stk_pay": "'.$acctNum.'", "nh_pay": "'.$nhbank.'"}';

        return;
    }
    echo '{"code": "01", "text": "Thanh toán Atm lỗi. Bạn vui lòng chọn phương thức thanh toán khác"}';
} catch (Exception $e) {
    echo '{"code": "99", "text": "Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ."}';
}