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
$search = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');

if ($search) {
    $query = "
        SELECT * 
        FROM `user` 
        WHERE `user` LIKE '%" . $conn->real_escape_string($search) . "%' 
        ORDER BY `user_id` DESC
    ";
    $result = $conn->query($query);
    $totalCount = $result->num_rows;
} else {
    $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $page = $page < 1 ? 1 : $page;
    $limit = 1; 
    $offset = ($page - 1) * $limit;
    $query = "
        SELECT * 
        FROM `user` 
        ORDER BY `user_id` DESC
        LIMIT $limit OFFSET $offset
    ";
    $result = $conn->query($query);

    $countQuery = "
        SELECT COUNT(*) AS total 
        FROM `user`
    ";
    $countResult = $conn->query($countQuery);
    $totalCount = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalCount / $limit);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>
    <link rel="stylesheet" href="../path/to/bootstrap.css">
</head>

<body>
    <div class="box-nf" id="box-nf"></div>
    <script type="text/javascript" src="../JavaScript/box.js"></script>
    <script language="javascript" src="../JavaScript/jquery-2.0.0.min.js"></script>
    <script language="javascript">
    function deletealluser() {
        box_nf("Quá trình đang thực hiện, vui lòng chờ trong giây lát", 2);
        $.ajax({
            url: "../archive/result.php",
            type: "post",
            dataType: "text",
            data: {
                type: 2
            },
            success: function(result) {
                box_nf(result, 3);
            }
        });
    }
    </script>
    <div class="bg-content" style="background: rgb(4, 58, 52); border-radius: 1rem; padding:10px">
        <div style="text-align:center;">
            <h4><?php echo htmlspecialchars($user['user']); ?> đang vào quản lý</h4>
        </div>
        <div class="container mb-2">
            <div class="row text-center justify-content-center g-2 mt-1">
                <div class="col-12 col-md-4 col-lg-3">
                    <a class="btn btn-success w-100 fw-semibold" href="/admin/home">Quay lại</a>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <a class="btn btn-success w-100 fw-semibold" onclick="deletealluser();">Delete All TK chưa Active</a>
                </div>
            </div>
        </div>
        <div>
            <form action="" method="post">
                <input type="text" name="search" class="form-control" placeholder="Tên tài khoản" value="<?php echo htmlspecialchars($search); ?>">
                <input style="margin-top: 10px;" class="btn btn-success col-1 fw-semibold" type="submit" name="submit" value="Tìm kiếm">
            </form>
        </div>
        <div class="mt-2">
            <?php if ($search && $result && $totalCount > 0): ?>
                <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
                    <table class="table text-white fw-semibold mb-0" role="table">
                        <thead>
                            <tr class="text-start fw-bold text-uppercase gs-0">
                                <th>#</th>
                                <th>Tài Khoản</th>
                                <th>Mật Khẩu</th>
                                <th>Mật Khẩu Cấp 2</th>
                                <th>Trạng Thái</th>
                                <th>Chỉnh Sửa</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $i = 1;
                            while ($user = $result->fetch_assoc()) {
                                $army = $conn->query("SELECT `id` FROM `armymem` WHERE `id` = " . (int)$user['user_id'] . ";");
                                echo '
                                    <tr>
                                        <td>' . $i . '</td>
                                        <td>' . htmlspecialchars($user["user"]) . '</td>
                                        <td>' . htmlspecialchars($user["password"]) . '</td>
                                        <td>' . htmlspecialchars($user["password2"]) . '</td>
                                        <td>' . ($army->num_rows != 0 ? 'Đã xác nhận' : 'Chưa xác nhận') . '</td>
                                        <td>' . ($army->num_rows != 0 ? '<a href="/admin/edit?id='. $user["user_id"] .'">Chỉnh Sửa</a>' : 'Chưa xác nhận') . '</td>
                                    </tr>
                                ';
                                $i++;
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($search): ?>
                <div class="alert alert-warning" role="alert">
                    Không tìm thấy tài khoản nào với từ khóa "<?php echo htmlspecialchars($search); ?>".
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php
$conn->close();
?>