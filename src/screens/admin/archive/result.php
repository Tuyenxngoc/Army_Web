<?php
require_once(__DIR__ . '/../../../../core/configs.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST data: ' . print_r($_POST, true));

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

    switch($_POST['type']) {
        case 2:
            if (!$isLogged)
                die('cần đăng nhập');
            $result = $conn->query("SELECT `user_id` FROM `user`;");
            $scdl = 0;
            while ($user = $result->fetch_assoc()) {
                $army = $conn->query("SELECT `id` FROM `armymem` WHERE `id` = ". $user['user_id'] ."; ");
                if ($army->num_rows == 0) {
                    if ($conn->query("DELETE FROM `user` WHERE `user_id` = '". $user['user_id'] ."'; "))
                    $scdl++;
                }
            }
            die('Xóa thành công '. $scdl .' / '. $result->num_rows);
            break;
        case 3:
            if (!$isLogged)
                die('cần đăng nhập');
            $user_id = $_POST['user_id'];
            $uname = $_POST['uname'];
            $password = $_POST['password'];
            $password2 = $_POST['password2'];
            $lock = $_POST['lock'];
            $email = $_POST['email'];
            $active = $_POST['active'];
			
            $CSinh = $_POST['CSinh'];
            $NVCSinh = $_POST['NVCSinh'];
            $xu = $_POST['xu'];
            $xu_khoa = $_POST['xukhoa'];
            $luong = $_POST['luong'];
            $XPMax = $_POST['XPMax'];
            $dvong = $_POST['dvong'];
            $vip_level = $_POST['vip_level'];			
            $item = $_POST['item'];
            $ruongItem = $_POST['ruongItem'];
            $ruongTB = $_POST['ruongTB'];
            for ($i = 0; $i < 10; $i++) {
                $NV[($i+1)] = $_POST['NV'. ($i + 1)];
            }
            $isuser = $conn->query("UPDATE `user` SET `user` = '". $uname ."', `password` = '". $password ."', `password2` = '". $password2 ."', `email` = '". $email ."', `active` = '". $active ."', `lock` = '". $lock ."' WHERE `user_id` = '". $conn->real_escape_string($user_id) ."'; ");
            $isarmy = $conn->query("UPDATE `armymem` SET `CSinh` = '". $CSinh ."', `NVCSinh` ='". $NVCSinh ."', `xu_khoa` = '". $xu_khoa ."', `xu` = '". $xu ."', `luong` = '". $luong ."', `XPMax` = '". $XPMax ."', `dvong` = '". $dvong ."', `vip_level` = '". $vip_level ."', `item` = '". $item ."', `ruongItem` = '". $ruongItem ."', `ruongTrangBi` = '". $ruongTB ."' WHERE `id` = '". $conn->real_escape_string($user_id) ."'; ");
             for ($i = 0; $i < 10; $i++) {
                 $conn->query("UPDATE `armymem` SET `NV". ($i + 1) ."` = '". $NV[($i + 1)] ."' WHERE `id` = '". $conn->real_escape_string($user_id) ."'; ");
             }
             if ($isuser && $isarmy)
                die('success');
            break;
        case 4:
            if (!$isLogged)
                die('cần đăng nhập');
            $giftcode = $conn->real_escape_string($_POST['giftcode']);
            $giftlimit = $conn->real_escape_string($_POST['giftlimit']);
            $giftpublic = $conn->real_escape_string($_POST['giftpublic']);
            $gifttime = $conn->real_escape_string($_POST['giftexpire']);
            $giftitem = $conn->real_escape_string($_POST['giftitem']);
            
            $sql = "INSERT INTO `giftcode` (`code`, `limit`, `public`, `expire`, `Item`) VALUES ('". $giftcode ."', '". $giftlimit ."', '". $giftpublic ."', '". $giftexpire ."', '". $giftitem ."')";
            if ($conn->query($sql))
                die('success');
            echo $giftcode .' '. $giftlimit .' '. $giftpublic .' '. $giftexpire .' '. $giftitem;
            break;
        case 5:
            if (!isset($_SESSION['user'])) {
                die('cần đăng nhập');
            }
            $idcode = $conn->real_escape_string($_POST['code_id']);
            $result = $conn->query("DELETE FROM `giftcode` WHERE `id` = '{$idcode}'");

            if ($result) {
                die('success');
            } else {
                error_log('Delete query failed: ' . $conn->error);
                die('Thất bại');
            }
            break;
        case 6:
            if (!$isLogged)
                die('cần đăng nhập');
            $idcode = $conn->real_escape_string($_POST['code_id']);
            $giftcode = $conn->real_escape_string($_POST['giftcode']);
            $giftlimit = $conn->real_escape_string($_POST['giftlimit']);
            $giftiddblist = $conn->real_escape_string($_POST['giftiddblist']);
            $giftpublic = $conn->real_escape_string($_POST['giftpublic']);
            $gifttime = $conn->real_escape_string($_POST['giftexpire']);
            $giftitem = $conn->real_escape_string($_POST['giftitem']);
            
            if ($conn->query("UPDATE `giftcode` SET `code` = '". $giftcode ."', `limit` = '". $giftlimit ."', `iddblist` = '". $giftiddblist ."', `public` = '". $giftpublic ."', `expire` = '". $giftexpire ."', `Item` = '". $giftitem ."' WHERE `id` = '". $idcode ."'; "))
                die('success');
            die('Thất bại');
            break;
    }
}