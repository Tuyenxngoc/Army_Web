<?php
define('NP', true);
require(__DIR__ . '/../../core/configs.php');

session_start();
if (!isset($_SESSION['user'])) {
    echo json_encode(['code' => '01', 'text' => 'Chưa đăng nhập']);
    exit;
}

$user = $_SESSION['user'];
$user_id = isset($user['user_id']) ? (int)$user['user_id'] : 1;

$createdCharacter = false;
$armymem = array();
$nv = array();

$conn = SQL();
$conn->query("SET NAMES utf8");

// Lấy dữ liệu người dùng
$stmt = $conn->prepare("SELECT * FROM `user` WHERE `user_id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Lấy dữ liệu armymem
$stmt = $conn->prepare("SELECT * FROM `armymem` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$armymem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!empty($armymem)) {
    $createdCharacter = true;
    $nvKey = 'NV' . $armymem['NVused'];
    if (isset($armymem[$nvKey])) {
        $nv = json_decode($armymem[$nvKey], true);
    } else {
        $nv = [];
    }
} else {
    $nv = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = json_decode(file_get_contents('php://input'), true);

    if (isset($postData['type']) && $postData['type'] === 'addpoint') {
        $point = isset($nv['point']) ? $nv['point'] : 0;
        $pointAdd = isset($nv['pointAdd']) ? $nv['pointAdd'] : array_fill(0, 5, 0);
        $totalPoint = 0;
        $error = false;
        $errorMessage = '';

        for ($i = 0; $i < 5; $i++) {
            $add = isset($postData['add'.$i]) ? (int)$postData['add'.$i] : 0;

            if ($add === "" || !preg_match('/^[0-9]+$/', $add)) {
                $error = true;
                $errorMessage = 'Giá trị điểm không hợp lệ!';
                break;
            }
            if ($add > 1000) {
                $error = true;
                $errorMessage = 'Tối đa 1000 điểm cho mỗi dòng!';
                break;
            }
            if ($add < 0) {
                $error = true;
                $errorMessage = 'Nhập điểm không hợp lệ!';
                break;
            }

            $pointAdd[$i] += $add;
            $totalPoint += $add;
        }

        if ($totalPoint <= 0) {
            $error = true;
            $errorMessage = 'Vui lòng nhập số điểm hợp lệ!';
        }

        if (!$error && $totalPoint > $point) {
            $error = true;
            $errorMessage = 'Số điểm không đủ!';
        }

        if ($error) {
            echo json_encode(['code' => '02', 'text' => $errorMessage]);
            exit;
        }

        $point -= $totalPoint;
        $nv["pointAdd"] = $pointAdd;
        $nv["point"] = $point;
        $armymem[$nvKey] = json_encode($nv);

        $sql = "UPDATE `armymem` SET `$nvKey` = ? WHERE `id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $armymem[$nvKey], $user_id);

        if ($stmt->execute()) {
            echo json_encode([
                'code' => '00',
                'text' => 'Điểm đã được thêm thành công!',
                'point' => $point,
                'pointAdd' => $nv['pointAdd']
            ]);
        } else {
            echo json_encode(['code' => '03', 'text' => 'Đã xảy ra lỗi khi cập nhật điểm.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['code' => '04', 'text' => 'Loại yêu cầu không hợp lệ!']);
    }

    $conn->close();
    exit;
}
?>
