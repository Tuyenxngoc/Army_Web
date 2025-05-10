<?php

define('NP', true);
require(__DIR__ . '/../../core/configs.php');

$post = json_decode(file_get_contents('php://input'), true);

try {
    $pCoin = $post['pcoin'];
    session_start();
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
    SQL()->query('INSERT INTO user_locks (user_id) VALUES (' . $userId . ') ON DUPLICATE KEY UPDATE locked_at = CURRENT_TIMESTAMP');
    $sqlUs = 'SELECT balance FROM user WHERE user_id = ' . $userId . ' LIMIT 1';
    $resultUs = SQL()->query($sqlUs);
    $userDB = $resultUs->fetch_assoc();
    $balanceBefore = $userDB['balance'];

    $sqlArmymem = 'SELECT online, luong FROM armymem WHERE id = ' . $userId . ' LIMIT 1';
    $resultArmymem = SQL()->query($sqlArmymem);
    $armymemDB = $resultArmymem->fetch_assoc();
    $isOnline = $armymemDB['online'];
    $luongBefore = $armymemDB['luong'];

    if ($isOnline == 1) {
        echo '{"code": "99", "text": "Bạn chưa thoát game."}';
        return;
    }

    if (!$armymemDB) {
        echo '{"code": "01", "text": "Chưa tạo nhân vật."}';
        return;
    }

    if ($luongBefore > 2000000000) {
        echo '{"code": "01", "text": "Số lượng hiện tại đã vượt quá 2 tỷ."}';
        return;
    }

    $luongChange = 0;
    foreach ($configDoiLuong as $item) {
        if ($item['pCoin'] == $pCoin) {
            $luongChange = $item['luong'] + ($item['luong'] * $bonusDoiLuong['bonus'] / 100);
            $luongAfter = $luongBefore + $luongChange;
            break;
        }
    }

    if ($luongAfter > 2000000000) {
        echo '{"code": "01", "text": "Số lượng không được vượt quá 2 tỷ."}';
        return;
    }

    if ($balanceBefore >= $pCoin || $pCoin == 0) {
        if ($pCoin > 0) {
            $balanceAfter = $balanceBefore - $pCoin;
            $sqlUpdateUser = 'UPDATE user SET balance = ' . $balanceAfter . ' WHERE user_id = ' . $userId;
            SQL()->query($sqlUpdateUser);
        }

        $sqlUpdateArmymem = 'UPDATE armymem SET luong = ' . $luongAfter . ' WHERE id = ' . $userId;
        SQL()->query($sqlUpdateArmymem);
        echo '{"code": "00", "text": "Bạn đã đổi Coin thành công"}';
    } else {
        echo '{"code": "01", "text": "Vui lòng nạp thêm tiền."}';
    }
    
    SQL()->query('DELETE FROM user_locks WHERE user_id = ' . $userId);
} catch (Exception $e) {
    SQL()->query('DELETE FROM user_locks WHERE user_id = ' . $userId);
    echo '{"code": "01", "text": "' . $e->getMessage() . '"}';
}
?>
