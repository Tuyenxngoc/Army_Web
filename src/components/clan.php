<?php 
$user = $_SESSION['user'];
$user_id = isset($user['user_id']) ? (int)$user['user_id'] : 1;
$conn = SQL();
$message = '';
$stmt = $conn->prepare("SELECT * FROM `armymem` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$armymem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (isset($_POST['join'])) {
    if ($armymem['request_clan'] == 0 && $armymem['clan'] == 0) {
        $idClan = $conn->real_escape_string($_POST['clan_id']);
        $sql = "SELECT * FROM `clan` WHERE `id` = '". $idClan ."' LIMIT 1;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $clan = $result->fetch_assoc();
            $nv = json_decode($armymem['NV'.$armymem['NVused']], true);
            if ($clan['mem'] >= $clan['memMax']) {
                echo "<br /><font color='red'>* Lỗi: Đội này đã đủ số thành viên cho phép.</font><br />";
            } else if ($nv['lever'] < 2) {
                echo "<br /><font color='red'>* Lỗi: Nhân vật bạn đang chọn phải hơn cấp 2.</font><br />";
            } else {
                $sql = "UPDATE `armymem` SET `clan` = '". $clan['id'] ."', `request_clan` = '". $clan['id'] ."' WHERE `id` = '". $armymem['id'] ."';";
                $conn->query($sql);
                $time = date("Y-m-d H:i:s");
                $stmt = $conn->prepare("INSERT INTO `clanmem` (`clan`, `user`, `user_name`, `timejoin`, `rights`)
                VALUES (?, ?, ?, ?, 0)");
                $stmt->bind_param("iiss", $idClan, $user['user_id'], $user['user'], $time);
                $stmt->execute();
                $stmt->close();
                header('Location: /squad/info');
                exit;
            }
        } else {
            echo "<br /><font color='red'>* Lỗi: Không tìm thấy đội.</font><br />";
        }
    } else {
        echo "<br /><font color='red'>* Lỗi: Bạn đã tham gia một đội hoặc đã gửi yêu cầu gia nhập đội khác.</font><br />";
    }
}
?>

<div class="modal fade" id="modalClan" tabindex="-1" aria-labelledby="modalClanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #007e70; border-color: #198754;">
            <div class="modal-body">
                <div class="text-center fw-semibold">
                    <div class="fs-6 mb-2">
                        <div class="d-flex justify-content-center align-items-center">
                            <img id="clanIcon" src="https://cdn.army2d.com/items/clan/151.png" alt="37 Group" width="16">
                            <span id="clanName" class="ms-1 fw-semibold" ></span>
                        </div>
                        <small id="clanInfo"></small>
                    </div>
                    <div class="mt-2">
                        <form action="" method="post">
                            <input type="hidden" name="clan_id" id="clan_id">
                            <button type="submit" name="join" id="clanJoin" class="btn btn-warning btn-sm text-dark fw-semibold" style="font-size: 12px; padding-top: 3px; padding-bottom: 4px;">
                                Tham gia
                            </button>
                        </form>
                    </div>
                </div>

                <table class="table table-borderless text-white" id="clanDetailsTable">
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Đội trưởng</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Thành viên</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cấp độ</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cúp</td>
                            <td><img src="/images/cup.png" alt="Cup" width="16"> <span class="ms1">0</span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Ngân sách</td>
                            <td><img src="/images/coin.png" alt="Xu" width="14"> <span class="ms1"></span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Thành lập</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>