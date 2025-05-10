<?php
define('NP', true);
include_once '../../CMain/connect.php';

function __query($sql) {
    global $conn;

    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return $result;
}

$sql = "SELECT * FROM users";
$result = __query($sql);

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => '',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));
$response = curl_exec($curl);
curl_close($curl);
$result = json_decode($response, true);
//echo "<pre>";
//print_r($result);
//echo "</pre>";

if (is_array($result) && isset($result['transactions'])) {
    foreach ($result['transactions'] as $data) {
        $description = $data['description'];
        $pos_nt = strpos($description, 'nt ');
        if ($pos_nt !== false) {
            $start_pos = $pos_nt + strlen('nt ');
            $rest_of_description = substr($description, $start_pos);
            $end_pos = strpos($rest_of_description, ' ');
            if ($end_pos === false) {
                $user = preg_replace('/[^\p{L}\p{N}\s]/u', '', $rest_of_description);
            } else {
                $user = substr($rest_of_description, 0, $end_pos);
                $user = preg_replace('/[^\p{L}\p{N}\s]/u', '', $user);
                if (substr($user, -2) === 'CT') {
                    $user = substr($user, 0, -2);
                }
            }
            $tranId = $data['transactionID'];
            $amount = $data['amount'];
            $transactionDate = $data['transactionDate'];

            $userIdQuery = "SELECT id FROM users WHERE username = '$user'";
            $resultUserId = $conn->query($userIdQuery);
            if ($resultUserId && $resultUserId->num_rows > 0) {
                $checkTransactionQuery = "SELECT * FROM `atm_bank` WHERE `tranid` = '$tranId'";
                $resultCheckTransaction = $conn->query($checkTransactionQuery);
                if ($resultCheckTransaction && $resultCheckTransaction->num_rows == 0) {
                    $username = $user;
                    $currentDate = date("Y-m-d H:i:s");
                    $bonus = 0;
                    foreach ($list_recharge_price_atm as $item) {
                        if ($item['amount'] == $amount) {
                            $bonus = $item['bonus'];
                            break;
                        }
                    }
                    $received = $amount + ($amount * $bonus / 100);
                    $insertQuery = "INSERT INTO `atm_bank`(`message`, `tranid`, `amount`, `received`, `created_at`, `updated_at`) VALUES ('$username','$tranId','$amount','$received', '$currentDate', '$currentDate')";
                    if ($conn->query($insertQuery) === TRUE) {
                        $updateQuery = "UPDATE `user` SET `balance` = `balance` + '$received', `tongnap` = `tongnap` + '$amount' WHERE `user` = '$username'";
                        $conn->query($updateQuery);
                    } else {
                    }
                } else {
                }
            } else {
            }
        } else {
        }
    }
} else {
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Nap</title>
</head>
<body>
    <script>
        setTimeout(function () {
            location.reload();
        }, 10000);
    </script>
</body>
</html>
