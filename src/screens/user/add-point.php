<?php
require_once(__DIR__ . '/../../../core/configs.php');

if (!isset($_SESSION['user'])) {
    header('Location: /home');
    exit;
}

$user = $_SESSION['user'];
$user_id = isset($user['user_id']) ? (int)$user['user_id'] : 1;

$createdCharacter = false;
$armymem = array();

$conn = SQL();
$conn->query("SET NAMES utf8");

$stmt = $conn->prepare("SELECT * FROM `user` WHERE `user_id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM `armymem` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$armymem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!empty($armymem)) {
    $createdCharacter = true;
}

$conn->close();

$nv = !empty($armymem['NV'.$armymem['NVused']]) ? json_decode($armymem['NV'.$armymem['NVused']], true) : array();

?>
<div class="title">
    <h4>Điểm Đã Cộng</h4>
</div>
<div class="mt-2">
    <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
        <table class="table text-white fw-semibold mb-0" role="table">
            <tbody class="fw-semibold" role="rowgroup">
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Sinh Lực</span>

                        </div>
                    </td>
                    <td role="cell" class="">
                        <div><span
                                id="point0"><?php echo htmlspecialchars(isset($nv['pointAdd'][0]) ? $nv['pointAdd'][0] : 'N/A'); ?></span>
                        </div>
                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Sức Mạnh</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <div><span
                                id="point1"><?php echo htmlspecialchars(isset($nv['pointAdd'][1]) ? $nv['pointAdd'][1] : 'N/A'); ?></span>
                        </div>
                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Phòng Thủ</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <div><span
                                id="point2"><?php echo htmlspecialchars(isset($nv['pointAdd'][2]) ? $nv['pointAdd'][2] : 'N/A'); ?></span>
                        </div>

                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">May Mắn</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <div><span
                                id="point3"><?php echo htmlspecialchars(isset($nv['pointAdd'][3]) ? $nv['pointAdd'][3] : 'N/A'); ?></span>
                        </div>

                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Đồng Đội</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <div><span
                                id="point4"><?php echo htmlspecialchars(isset($nv['pointAdd'][4]) ? $nv['pointAdd'][4] : 'N/A'); ?></span>
                        </div>
                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold"></span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <div>
                            <span id="point4">
                                <div id="point">Tổng Điểm Chưa Cộng:
                                    <?php echo htmlspecialchars(isset($nv['point']) ? number_format($nv['point'], 0, ',', '.') : 'N/A'); ?>
                                </div>
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="title">
    <h4>Cộng Điểm</h4>
</div>
<div id="message" style="text-align: center;"></div>
<div class="mt-2">
    <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
        <table class="table text-white fw-semibold mb-0" role="table">
            <tbody class="fw-semibold" role="rowgroup">
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Sinh Lực</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <input id="addp0" type="number" value="0">
                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Sức Mạnh</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <input id="addp1" type="number" value="0">
                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Phòng Thủ</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <input id="addp2" type="number" value="0">
                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">May Mắn</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <input id="addp3" type="number" value="0">
                    </td>
                </tr>
                <tr role="row">
                    <td role="cell" class="">
                        <div class="cursor-pointer">
                            <span class="ms-2 fw-semibold">Đồng Đội</span>
                        </div>
                    </td>
                    <td role="cell" class="">
                        <input id="addp4" type="number" value="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-center mt-3">
            <button class="btn btn-success me-2 px-3 py-1" onclick="addpoint()">Cộng</button>
        </div>
        <br>
    </div>
</div>
<style>
input[type="number"] {
    width: 100%;
    padding: 0.2rem;
    margin: 0.2rem 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f8f8f8;
    color: #333;
    text-align: center;
    font-size: 1rem;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: border-color 0.2s ease-in-out;
}

input[type="number"]:focus {
    border-color: #66afe9;
    outline: none;
    box-shadow: 0 0 8px rgba(102, 175, 233, 0.6);
}
</style>
<script>
function addpoint() {
    var isValid = true;
    for (var i = 0; i < 5; i++) {
        var c = $('#addp' + i).val();
        if (c == null || c === '' || c < 0 || isNaN(c)) {
            $('#message').html(
                '<span class="invalid-feedback" role="alert" style="color:red"><strong>Vui lòng nhập điểm hợp lệ</strong></span><br>'
                );
            isValid = false;
            break;
        }
        if (c > 1000) {
            $('#message').html(
                '<span class="badge badge-danger"><font size="4">Số điểm cộng tối đa là 1000 !</font></span>');
            isValid = false;
            break;
        }
    }
    if (!isValid) {
        return;
    }
    $('#message').html(
        '<span class="invalid-feedback" role="alert" style="color:blue"><strong>Đang xử lý</strong></span><br>');
    var addData = {
        type: 'addpoint',
        add0: $('#addp0').val(),
        add1: $('#addp1').val(),
        add2: $('#addp2').val(),
        add3: $('#addp3').val(),
        add4: $('#addp4').val()
    };
    $.ajax({
        url: "/apixuli/update",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(addData),
        dataType: "json",
        success: function(data) {
            if (data.code === '00') {
                $('#point').text(data.point);
                $('#point0').text(data.pointAdd[0]);
                $('#point1').text(data.pointAdd[1]);
                $('#point2').text(data.pointAdd[2]);
                $('#point3').text(data.pointAdd[3]);
                $('#point4').text(data.pointAdd[4]);
                $('#message').html(
                    '<span class="badge badge-success"><font size="2">Cộng điểm thành công!</font></span>'
                    );

                $('#addp0').val(0);
                $('#addp1').val(0);
                $('#addp2').val(0);
                $('#addp3').val(0);
                $('#addp4').val(0);
            } else {
                $('#message').html('<span class="badge badge-danger"><font size="4">' + data.text +
                    '</font></span>');
            }
        },
        error: function() {
            $('#message').html('<span class="badge badge-danger"><font size="2">Xảy ra lỗi!</font></span>');
        }
    });
}
</script>