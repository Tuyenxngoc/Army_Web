<?php

defined('NP') or header('location: /');
date_default_timezone_set("Asia/Ho_Chi_Minh");
function SQL() {
    $servername = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    $dbname = $_ENV['DB_NAME'];
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    
    $conn->set_charset('utf8');
    return $conn;
}

function now($time = NULL)
{
    return isset($time) ? date("H:i:s d/m/Y", $time) : date("H:i:s d/m/Y");
}

function is_login()
{
    if (isset($_SESSION['user']) || isset($_SESSION['isLogged'])) {
        return true;
    } else {
        return false;
    }
}
function user($find, $__value = NULL)
{
    if ($__value == NULL) {
        if (is_login()) {
            $result = __query("SELECT * FROM user WHERE user = '" . $_SESSION['user'] . "'");
            if ($result) {
                $user = $result->fetch_array();
                return isset($user[$find]) ? $user[$find] : null;
            }
        }
        return false;
    } else {
        $result = __query("SELECT * FROM user WHERE user_id = $__value");
        if ($result) {
            $user = $result->fetch_array();
            return isset($user[$find]) ? $user[$find] : null;
        }
        return false;
    }
}

function widget($path, $variables = [])
{
    extract($variables);
    require __DIR__ . '/../pages/' . $path . '.php';
}

function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    
    curl_close($ch);
    return $data;
}

function curlPost($url, $dataPost = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($dataPost) ? json_encode($dataPost) : $dataPost);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
        )
    );

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return 'Lỗi';
    } else {
        return $response;
    }
}

function getConfigNapThe()
{
    global $configNapCard;
    return json_decode(json_encode($configNapCard));
}

function getConfigMomo()
{
    global $configNapTien;
    return json_decode(json_encode($configNapTien))->momo;
}

function getConfigAtm()
{
    global $configNapTien;
    return json_decode(json_encode($configNapTien))->atm;
}

function getTransfer()
{
    global $configTransfer;
    return json_decode(json_encode($configTransfer));
}

function getNoiDungNapTien()
{
	return 'naptien'.(string) user('user_id');
}

function getNetworkCode()
{

    foreach (getConfigNapThe()->networkCode as $key => $value) {
        echo '<option value="' . $key . '">' . $key . '(' . $value . '%)</option>';
    }
}

function getStatusCard($status)
{
    switch (strtolower($status)) {
        case 'thanhcong':
            $return = '<b style="color: green;">Thành công</b>';
            break;
        case 'thatbai':
            $return = '<b style="color: red;">Thất bại</b>';
            break;
        case 'saimenhgia':
            $return = '<b style="color: orange;">Sai mệnh giá</b>';
            break;
        default:
            $return = '<b style="color: blue;">Chờ xử lý</b>';
            break;
    }
    return $return;
}

function __insert($table, $dataInsert = [])
{
    $sqlInsert = '';

    foreach ($dataInsert as $key => $value) {
        $sqlInsert .= "`$key` = '" . $value . "',";
    }

    $result = __query("INSERT INTO $table SET " . trim($sqlInsert, ',') . "");
    return $result;
}

function __update($table, $dataUpdate = [], $where = [])
{
    $sqlUpdate = '';
    $sqlWhere = '';

    foreach ($dataUpdate as $key => $value) {
        $sqlUpdate .= "`$key` = '" . $value . "',";
    }

    foreach ($where as $key => $value) {
        $sqlWhere .= "$key = '" . $value . "' AND";
    }

    $sqlWhere = trim($sqlWhere, " AND");

    $result = __query("UPDATE `$table` SET " . trim($sqlUpdate, ',') . " WHERE " . $sqlWhere);
    return $result;
}

function __delete($table, $where = [])
{
    $sqlWhere = '';
    foreach ($where as $key => $value) {
        $sqlWhere .= "$key = '" . $value . "' AND";
    }

    $sqlWhere = trim($sqlWhere, " AND");

    $result = __query("DELETE FROM `$table` WHERE " . $sqlWhere);
    return $result;
}

function __list($table, $where = [], $protectedRow = [])
{
    $query = __select($table, $where);
    $result = [];
    foreach ($query as $value) {

        if ((gettype($protectedRow) == 'array') || $protectedRow != []) {

            foreach ($protectedRow as $row) {
                unset($value[$row]);
            }

            $result[] = $value;
        }
    }
    return $result;
}

function __fetchArray($table, $where = [])
{
    return __select($table, $where)->fetch_array();
}

function __numRows($table, $where = [])
{
    return (int) __select($table, $where)->num_rows;
}

function __select($table, $where = [])
{
    if (gettype($where) == 'array') {
        $sqlWhere = '';
        if (count($where) >= 1) {

            foreach ($where as $key => $value) {
                $sqlWhere .= "$key = '" . $value . "' AND";
            }
            $sqlWhere = trim($sqlWhere, " AND");

            $result = __query("SELECT * FROM `$table` WHERE " . $sqlWhere);
        } else {
            $result = __query("SELECT * FROM `$table`");
        }
        return $result;
    }

    return false;
}

function __query($sqlQuery)
{
    $result = SQL()->query($sqlQuery);
    return (!$result) ? __error() : $result;
}

function __error()
{
    return mysqli_error(SQL());
}

function parse_order_id($id)
{
    $re = '/' . 'naptien' . '\d+/im';
    preg_match_all($re, $id, $matches, PREG_SET_ORDER, 0);
    if (count($matches) == 0)
        return null;
    $orderCode = $matches[0][0];
    $prefixLength = strlen('naptien');
    $orderId = intval(substr($orderCode, $prefixLength));
    return $orderId;
}

function colorChat($rank, $user)
{
    switch ($rank) {
        case 1:
            $return = '<b style="color: red">' . $user . '</b>';
            break;

        default:
            $return = '<b>' . $user . '</b>';
            break;
    }
    return $return;
}

function getRandomString($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
  
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
  
    return $randomString;
}

function timeAgo($time_ago)
{
    $time_ago   = date("Y-m-d H:i:s", $time_ago);
    $time_ago   = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed;
    $minutes    = round($time_elapsed / 60);
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400);
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640);
    $years      = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return "$seconds giây trước";
    }
    //Minutes
    else if ($minutes <= 60) {
        return "$minutes phút trước";
    }
    //Hours
    else if ($hours <= 24) {
        return "$hours tiếng trước";
    }
    //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return "Hôm qua";
        } else {
            return "$days ngày trước";
        }
    }
    //Weeks
    else if ($weeks <= 4.3) {
        return "$weeks tuần trước";
    }
    //Months
    else if ($months <= 12) {
        return "$months tháng trước";
    }
    //Years
    else {
        return "$years năm trước";
    }
}

function getStatusUser($status)
{
    switch (strtolower($status)) {
        case 1:
            $return = '<b style="color: green;">Hoạt động</b>';
            break;
        case 2:
            $return = '<b style="color: red;">Bị khoá</b>';
            break;
        default:
            $return = '<b style="color: orange;">Chưa kích hoạt</b>';
            break;
    }
    return $return;
}

function getQrMomoPayment($username, $amount, $acctNum){
    return "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=2|99|$acctNum|||0|0|$amount|$username|transfer_p2p";
}
function getLinkMomoPayment($username, $amount, $acctNum){
    return "https://nhantien.momo.vn/$acctNum/$amount";
}

function getQrAtmPayment($username,$amount, $acctNum){
    return "https://api.vietqr.io/970416/$acctNum/$amount/nt $username/qr_only.jpg";
}
function getgiaAtmPayment($amount){
    return number_format($amount);
}
function calMoneyForUser($tongnap, $amount)
{
    if ($tongnap >= 5000000) {
        return caculatorMoney(3, $amount);
    } else if ($tongnap < 5000000 && $tongnap >= 2000000) {
        return caculatorMoney(2, $amount);
    } else if ($tongnap < 2000000 && $tongnap >= 1000000) {
        return caculatorMoney(1, $amount);
    }
    return caculatorMoney(0, $amount);
}

function caculatorMoney($categoryCustom, $amount)
{
    global $configBonusCharge;
    switch ($categoryCustom) {
        case 0:
            return moneyBonus($amount, $configBonusCharge[0]['bn0'], $configBonusCharge[0]['bn1'], $configBonusCharge[0]['bn2'], $configBonusCharge[0]['bn3'], $configBonusCharge[0]['bn4'], $configBonusCharge[0]['bn5']);
        case 1:
            return moneyBonus($amount, $configBonusCharge[1]['bn0'], $configBonusCharge[1]['bn1'], $configBonusCharge[1]['bn2'], $configBonusCharge[1]['bn3'], $configBonusCharge[1]['bn4'], $configBonusCharge[1]['bn5']);
        case 2:
            return moneyBonus($amount, $configBonusCharge[2]['bn0'], $configBonusCharge[2]['bn1'], $configBonusCharge[2]['bn2'], $configBonusCharge[2]['bn3'], $configBonusCharge[2]['bn4'], $configBonusCharge[2]['bn5']);
        case 3:
            return moneyBonus($amount, $configBonusCharge[3]['bn0'], $configBonusCharge[3]['bn1'], $configBonusCharge[3]['bn2'], $configBonusCharge[3]['bn3'], $configBonusCharge[3]['bn4'], $configBonusCharge[3]['bn5']);
    }
}

function moneyBonus($amount, $bn0, $bn1, $bn2, $bn3, $bn4, $bn5)
{
    if ($amount < 200000) {
        return $amount + $amount * $bn0;
    } else if ($amount >= 200000 && $amount < 1000000) {
        return $amount + $amount * $bn1;
    } else if ($amount >= 1000000 && $amount < 2000000) {
        return $amount + $amount * $bn2;
    } else if ($amount >= 2000000 && $amount < 5000000) {
        return $amount + $amount * $bn3;
    } else if ($amount >= 5000000 && $amount < 10000000) {
        return $amount + $amount * $bn4;
    } else {
        return $amount + $amount * $bn5;
    }
}