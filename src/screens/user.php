<?php
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

$user = $_SESSION['user'];
$user_id = isset($user['user_id']) ? (int)$user['user_id'] : 1;
$conn = SQL();
$stmt = $conn->prepare("SELECT * FROM `armymem` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$armymem = $stmt->get_result()->fetch_assoc();
$stmt->close();

$linkBietDoi = "/squad";
if ($armymem && $armymem['clan'] > 0) {
    $linkBietDoi = "/squad/info";
}
?>
<div class="mb-3">
    <div class="row text-center justify-content-center row-cols-3 row-cols-lg-6 g-1 g-lg-1">
        <div class="col">
            <a class="btn btn-success me-2 px-3 py-1 <?php echo $tab != "profile" ? "active" : "false"; ?>"
                href="/user/profile" style="background-color: rgb(255, 180, 115);">Tài khoản
            </a>
        </div>
        <div class="col">
            <a class="btn btn-success me-2 px-3 py-1 <?php echo $tab == "change-password" ? "active" : "false"; ?>"
                href="/user/change-password" style="background-color: rgb(255, 180, 115);">Đổi Mật Khẩu
            </a>
        </div>
        <div class="col">
            <a class="btn btn-success me-2 px-3 py-1 <?php echo $tab == "change-gmail" ? "active" : "false"; ?>"
                href="/user/change-gmail" style="background-color: rgb(255, 180, 115);">Đổi Gmail
            </a>
        </div>
        <div class="col">
            <a class="btn btn-success me-2 px-3 py-1 <?php echo $tab == "change-password-two" ? "active" : "false"; ?>"
                href="/user/change-password-two" style="background-color: rgb(255, 180, 115);">Đổi MK Cấp 2
            </a>
        </div>
        <div class="col">
            <a class="btn btn-success me-2 px-3 py-1 <?php echo $tab == "add-point" ? "active" : "false"; ?>"
                href="/user/add-point" style="background-color: rgb(255, 180, 115);">Cộng Point
            </a>
        </div>
        <div class="col">
            <a class="btn btn-success me-2 px-3 py-1 <?php echo $tab == "squad" ? "active" : "false"; ?>"
                href="<?php echo $linkBietDoi; ?>" style="background-color: rgb(255, 180, 115);">Biệt Đội
            </a>
        </div>
    </div>
</div>
<hr>
<?php
switch ($tab) {
    case "profile":
        include_once('user/profile.php');
        break;
    case "squad":
        include_once('squad/views.php');
        break;
    case "change-password":
        include_once('user/change-password.php');
        break;
    case "change-gmail":
        include_once('user/change-gmail.php');
        break;
    case "change-password-two":
        include_once('user/change-password-two.php');
        break;
    case "add-point":
        include_once('user/add-point.php');
        break;
    default:
        include_once('user/profile.php'); 
        break;
}
?>