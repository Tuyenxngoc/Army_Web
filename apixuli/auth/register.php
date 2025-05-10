<?php

define('NP', true);
require(__DIR__ . '/../../core/configs.php');

$post = json_decode(file_get_contents('php://input'), true);
if (empty($post['username']) || empty($post['password']) || empty($post['email'])) {
    echo '{"code": "01", "text": "Vui lòng nhập đầy đủ thông tin đăng ký."}';
    exit;
}
$username = $post['username'];
$password = $post['password'];
$email = $post['email'];
if (!preg_match('/^[a-z0-9_]+$/', $username) || !preg_match('/^[a-z0-9_]+$/', $password)) {
    echo json_encode(['code' => '01', 'text' => 'Tên đăng nhập và mật khẩu chỉ được chứa chữ thường, số và dấu gạch dưới.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['code' => '01', 'text' => 'Email không hợp lệ.']);
    exit;
}
$conn = SQL();
try {
    $stmt = $conn->prepare("SELECT `user` FROM `user` WHERE `user` = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['code' => '02', 'text' => 'Tên đăng nhập đã tồn tại.']);
        exit;
    }
    // $stmt = $conn->prepare("SELECT `email` FROM `user` WHERE `email` = ?");
    // $stmt->bind_param("s", $email);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // if ($result->num_rows > 0) {
    //     echo json_encode(['code' => '03', 'text' => 'Email đã tồn tại.']);
    //     exit;
    // }
    $stmt = $conn->prepare("INSERT INTO `user` (`user`, `password`, `email`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $email);
    if ($stmt->execute()) {
        echo json_encode(['code' => '00', 'text' => 'Đăng ký thành công.']);
    } else {
        echo json_encode(['code' => '99', 'text' => 'Hệ thống gặp lỗi. Vui lòng thử lại sau.']);
    }
} catch (Exception $e) {
    echo json_encode(['code' => '99', 'text' => 'Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ.']);
}

$conn->close();
?>
