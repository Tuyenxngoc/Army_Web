<?php
define('NP', true);
require(__DIR__ . '/../../core/configs.php');

$post = json_decode(file_get_contents('php://input'), true);
if (empty($post['username']) || empty($post['password'])) {
    echo json_encode(['code' => '01', 'text' => 'Thông tin tài khoản hoặc mật khẩu không được trống.']);
    exit;
}
$username = strip_tags($post['username']);
$password = strip_tags($post['password']);

if (!preg_match('/^[a-z0-9_]+$/', $username) || !preg_match('/^[a-z0-9_]+$/', $password)) {
    echo json_encode(['code' => '01', 'text' => 'Tên đăng nhập và mật khẩu chỉ được chứa chữ thường, số và dấu gạch dưới.']);
    exit;
}

$conn = SQL();
try {
    $stmt = $conn->prepare("SELECT `user_id`, `user`, `password` FROM `user` WHERE `user` = ? AND `password` = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['user_id'];

        $armymemQuery = "SELECT 1 FROM armymem WHERE id = ?";
        $armymemStmt = $conn->prepare($armymemQuery);
        $armymemStmt->bind_param("i", $userId);
        $armymemStmt->execute();
        $armymemResult = $armymemStmt->get_result();

        if ($armymemResult->num_rows > 0) {
            session_start();
            $_SESSION['user'] = $row;
            $_SESSION['isLogged'] = true;
            echo json_encode(['code' => '00', 'text' => 'Đăng nhập thành công.']);
        } else {
            echo json_encode(['code' => '02', 'text' => 'Vui lòng vào game tạo nhân vật trước khi đăng nhập.']);
        }
        $armymemStmt->close();
    } else {
        echo json_encode(['code' => '01', 'text' => 'Thông tin tài khoản hoặc mật khẩu không chính xác.']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'code' => '99',
        'text' => 'Hệ thống gặp lỗi. Vui lòng liên hệ quản trị viên để được hỗ trợ.',
    ]);
}
?>
