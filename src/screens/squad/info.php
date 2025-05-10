<?php

if (!isset($_SESSION['user'])) {
    header('Location: /home');
    exit;
}
$user = $_SESSION['user'];
$user_id = isset($user['user_id']) ? (int)$user['user_id'] : 1;
$conn = SQL();
$message = '';
$stmt = $conn->prepare("SELECT * FROM `armymem` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$armymem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($armymem && $armymem['clan'] > 0) {
    $clan_id = $armymem['clan'];

    $stmt = $conn->prepare("SELECT * FROM `clan` WHERE `id` = ? LIMIT 1");
    $stmt->bind_param("i", $clan_id);
    $stmt->execute();
    $clan = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM `clanmem` WHERE `clan` = ? ORDER BY `rights` DESC, `timeJoin` ASC");
    $stmt->bind_param("i", $clan_id);
    $stmt->execute();
    $clan_members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $NVused = null;
    if ($clan['master']) {
        $stmt = $conn->prepare("SELECT NVused FROM `armymem` WHERE `id` = ? LIMIT 1");
        $stmt->bind_param("i", $clan['master']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $NVused = $result['NVused'];
        $stmt->close();
    }

    $XPmax = 25000 * $clan['level'] * ($clan['level'] + 1);
    $xpPercentage = ($XPmax > 0) ? round(($clan['xp'] / $XPmax) * 100) : 0;

    $user_rights = null;
    $stmt = $conn->prepare("SELECT `rights` FROM `clanmem` WHERE `user` = ? AND `clan` = ?");
    $stmt->bind_param("ii", $user_id, $clan_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $user_rights = $result['rights'] ?? 0;
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
        if ($action == 'kick' && $user_rights == 2) {
            $stmt = $conn->prepare("DELETE FROM `clanmem` WHERE `user` = ? AND `clan` = ?");
            $stmt->bind_param("ii", $member_id, $clan_id);
            if ($stmt->execute()) {
                $updateStmt = $conn->prepare("UPDATE `armymem` SET `clan` = 0, `request_clan` = 0 WHERE `id` = ?");
                $updateStmt->bind_param("i", $member_id);
                if ($updateStmt->execute()) {
                    $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Đã xóa thành viên khỏi clan thành công .</div>';
                } else {
                    $_SESSION['update_message'] = '<div class="alert alert-warning" role="alert">Xóa thành viên thành công.</div>';
                }
                $updateStmt->close();
            } else {
                $_SESSION['update_message'] = '<div class="alert alert-danger" role="alert">Không thể xóa thành viên khỏi clan.</div>';
            }
            $stmt->close();
        } elseif ($action == 'promote') {
            if ($user_rights == 2) {
                $stmt = $conn->prepare("UPDATE `clanmem` SET `rights` = 2 WHERE `user` = ? AND `clan` = ?");
                $stmt->bind_param("ii", $member_id, $clan_id);
                $stmt->execute();
                $stmt->close();
                $stmt = $conn->prepare("UPDATE `clanmem` SET `rights` = 0 WHERE `user` = ? AND `clan` = ?");
                $stmt->bind_param("ii", $user_id, $clan_id);
                $stmt->execute();
                $stmt->close();
                $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Thành viên được thăng chức thành công.</div>';
            } elseif ($user_rights == 1 && $member_id == $user_id) {
                $stmt = $conn->prepare("UPDATE `clanmem` SET `rights` = 0 WHERE `user` = ? AND `clan` = ?");
                $stmt->bind_param("ii", $user_id, $clan_id);
                $stmt->execute();
                $stmt->close();
                $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Đã bị giáng chức thành viên thành công.</div>';
            } elseif ($user_rights == 1 && $member_id != $user_id) {
                $stmt = $conn->prepare("UPDATE `clanmem` SET `rights` = 1 WHERE `user` = ? AND `clan` = ?");
                $stmt->bind_param("ii", $member_id, $clan_id);
                $stmt->execute();
                $stmt->close();
                $stmt = $conn->prepare("UPDATE `clanmem` SET `rights` = 0 WHERE `user` = ? AND `clan` = ?");
                $stmt->bind_param("ii", $user_id, $clan_id);
                $stmt->execute();
                $stmt->close();
                $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Thành viên được thăng chức thành công.</div>';
            }
        } elseif ($action == 'demote' && $user_rights == 2) {
            $stmt = $conn->prepare("UPDATE `clanmem` SET `rights` = 1 WHERE `user` = ? AND `clan` = ?");
            $stmt->bind_param("ii", $member_id, $clan_id);
            if ($stmt->execute()) {
                $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Thành viên bị giáng chức thành công.</div>';
            } else {
                $message = '<div class="alert alert-danger" role="alert">Không thể giáng chức thành viên.</div>';
            }
            $stmt->close();
        } elseif ($action == 'leave' && $user_rights == 0) {
            $stmt = $conn->prepare("UPDATE `armymem` SET `clan` = 0, `request_clan` = 0 WHERE `id` = ?");
            $stmt->bind_param("i", $armymem['id']);
            $stmt->execute();
            $stmt->close();
            $sql = "UPDATE `armymem` SET `xu` = `xu` - 1000, `clanpoint` = `clanpoint` - 100 WHERE `id` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $armymem['id']);
            $stmt->execute();
            $stmt->close();

            $sql = "DELETE FROM `clanmem` WHERE `user` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $armymem['id']);
            $stmt->execute();
            $stmt->close();

            $sql = "UPDATE `clan` SET `mem` = `mem` - 1 WHERE `id` = ? AND `mem` > 0";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $clan_id);
            $stmt->execute();
            $stmt->close();
            header('Location: /squad');
            exit;
        } elseif  ($action == 'disperse-clan') {
            if (empty($clan_id)) {
                $_SESSION['update_message'] = '<div class="alert alert-danger" role="alert">Không có clan ID để giải tán.</div>';
                header('Location: /squad');
                exit;
            }
            $stmt = $conn->prepare("DELETE FROM `clan` WHERE `id` = ?");
            $stmt->bind_param("i", $clan_id);
            if ($stmt->execute()) {
                $stmt->close();
                $stmt = $conn->prepare("DELETE FROM `clanmem` WHERE `clan` = ?");
                $stmt->bind_param("i", $clan_id);
                if ($stmt->execute()) {
                    $stmt->close();
                    $updateStmt = $conn->prepare("UPDATE `armymem` SET `clan` = 0, `request_clan` = 0 WHERE `clan` = ?");
                    $updateStmt->bind_param("i", $clan_id);
                    if ($updateStmt->execute()) {
                        $updateStmt->close();
                    } else {
                        $updateStmt->close();
                        $_SESSION['update_message'] = '<div class="alert alert-danger" role="alert">Lỗi khi cập nhật thành viên không còn thuộc clan.</div>';
                    }
                } else {
                    $stmt->close();
                    $_SESSION['update_message'] = '<div class="alert alert-danger" role="alert">Lỗi khi xóa thành viên khỏi clan.</div>';
                }
            } else {
                $stmt->close();
                $_SESSION['update_message'] = '<div class="alert alert-danger" role="alert">Lỗi khi xóa clan.</div>';
            }
            header('Location: /squad');
            exit;
        } elseif  ($action == 'browse') {
            $stmt = $conn->prepare("SELECT request_clan, clan FROM armymem WHERE id = ?");
            $stmt->bind_param("i", $clan_id);
            $stmt->execute();
            $stmt->bind_result($request_clan, $clan);
            $stmt->fetch();
            $stmt->close();
            if ($clan == 0) {
                if ($request_clan == $clan_id) {
                    $update_stmt = $conn->prepare("UPDATE armymem SET clan = ? WHERE id = ?");
                    $update_stmt->bind_param("ii", $clan_id, $clan_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    echo "Member has been approved and assigned to the clan.";
                } else {
                    echo "The request does not match the current clan ID.";
                }
            } else {
                echo "Member has already been approved or is in a different clan.";
            }

        } elseif  ($action == 'approve') {
            $memberId = intval($_POST['member_id']);
            $clanId = intval($armymem['clan']);
            if ($memberId > 0 && $clanId > 0) {
                $sqlUpdate = "UPDATE armymem SET clan = ? WHERE id = ? AND request_clan = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("iii", $clanId, $memberId, $clanId);
                $result = $stmtUpdate->execute();
                $stmtUpdate->close();
            } 
            header('Location: /squad/info');
            exit();
        }
        header('Location: /squad/info');
        exit;
        
    }

    if (isset($_POST['save-desc']) && $armymem['clan'] > 0 && $armymem['id'] == $clan['master']) {
        $new_desc = trim($_POST['clan-desc']);
        if (strlen($new_desc) > 200) {
            $new_desc = substr($new_desc, 0, 200);
        }
        $stmt = $conn->prepare("UPDATE `clan` SET `desc` = ? WHERE `id` = ?");
        $stmt->bind_param("si", $new_desc, $clan_id);
        if ($stmt->execute()) {
            $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Cập nhật mô tả thành công.</div>';
        } else {
            $message = '<div class="alert alert-danger" role="alert">Không thể cập nhật mô tả.</div>';
        }
        $stmt->close();
        header('Location: /squad/info');
        exit;
    }

    if (isset($_POST['open-mem']) && $clan['luong'] >= 100 && $clan['memMax'] < 100) {
        $sql = "UPDATE `clan` SET `memMax` = `memMax` + 5, `luong` = `luong` - 100 WHERE `id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $armymem['clan']);
        if ($stmt->execute()) {
            $_SESSION['update_message'] = '<div class="alert alert-success" role="alert">Tăng cường số lượng thành viên thành công.</div>';
            header('Location: /squad');
            exit;
        } else {
            $message = '<div class="alert alert-danger" role="alert">Không thể tăng cường số lượng thành viên.</div>';
        }
        $stmt->close();
        header('Location: /squad/info');
        exit;
    } 
}
$newMembersClan = [];
if (isset($armymem['clan']) && $armymem['clan'] > 0) {
    $clan_id = $armymem['clan'];
    $sqlx = "SELECT id, request_clan FROM armymem WHERE request_clan = ? AND clan = 0";
    $stmtx = $conn->prepare($sqlx);
    $stmtx->bind_param("i", $clan_id);
    $stmtx->execute();
    $resultx = $stmtx->get_result();
    while ($row = $resultx->fetch_assoc()) {
        $userId = $row['id'];
        $sqlUser = "SELECT user FROM user WHERE user_id = ?";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bind_param("i", $userId);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();
        if ($userRow = $resultUser->fetch_assoc()) {
            $row['user'] = $userRow['user'];
        } else {
            $row['user'] = 'Unknown';
        }
        $newMembersClan[] = $row;
        $stmtUser->close();
    }
    $stmtx->close();
}
if (isset($_SESSION['update_message'])) {
    echo $_SESSION['update_message'];
    unset($_SESSION['update_message']);
}
?>
<div style="background: #007e70; border-radius: 1rem; padding:10px">
    <center>
        <div class="mb-1 d-flex align-items-center justify-content-center">
            <img class="ms-2" src="https://cdn.army2d.com/items/clan/146.png" alt="thanhdat" width="16" style="image-rendering: pixelated;">
            <span class="fw-bold ms-1"><?php echo htmlspecialchars($clan['name']) ?></span>
        </div>
        <div class="fw-semibold"><?php echo htmlspecialchars($clan['desc']) ?></div>
        <div class="d-flex align-items-center justify-content-center">
            <img src="/images/cup.png" alt="Cup" width="16">
            <span class="fw-semibold text-warning ms-1"><?php echo htmlspecialchars($clan['cup']) ?></span>
        </div>
        <div class="fw-semibold">Thành viên: <?php echo htmlspecialchars($clan['mem']) ?> / <?php echo htmlspecialchars($clan['memMax']) ?></div>
        <div class="fw-semibold">Ngân sách: <?php echo htmlspecialchars(number_format($clan['xu'])); ?> xu - <?php echo htmlspecialchars(number_format($clan['luong'])); ?> lượng</div>

        <div class="fw-semibold">Level: <?php echo htmlspecialchars($clan['level']) ?> + <?php echo htmlspecialchars($xpPercentage) ?>%</div>
        <div class="progress my-1" style="height: 18px; max-width: 160px; background-color: rgb(4, 58, 52); position: relative;">
            <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($xpPercentage); ?>%; background-color: rgb(0, 123, 255);"></div>
            <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: rgb(255, 255, 255); font-weight: bold; font-size: 12px;">
                <?php echo htmlspecialchars($clan['xp']); ?>/<?php echo htmlspecialchars($XPmax); ?>
            </span>
        </div>
        <div class="fw-semibold">Thành lập: <?php echo htmlspecialchars($clan['dateCreat']) ?></div>

        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'alert-success') !== false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="mt-2">
            <?php if ($user_rights == 2): ?>
                <button type="button" class="btn btn-warning small-button text-dark fw-semibold me-1" onclick="toggleConfigForm()">Cấu hình biệt đội</button>
                <div id="configForm" style="display:none;">
                    <?php if ($armymem['clan'] > 0 && $armymem['id'] == $clan['master']): ?>
                        <form action="" method="post">
                            Email: <?php echo htmlspecialchars($clan["email"]); ?>
                            <br />
                            Phone: <?php echo htmlspecialchars($clan["phone"]); ?>
                            <br />
                            Giới thiệu (tối đa 200 kí tự):
                            <br />
                            <textarea name="clan-desc" rows="2" cols="40"><?php echo htmlspecialchars($clan["desc"]); ?></textarea>
                            <br />
                            <input class="btn btn-warning small-button text-dark fw-semibold position-relative" type="submit" name="save-desc" value="Cập nhật">
                            <br />

                            <br />
                            <input class="btn btn-danger small-button fw-semibold position-relative" type="submit" name="open-mem" value="Mở thêm thành viên">
                            <br />
                            <br />
                        </form>
                    <?php endif; ?>
                </div>
                <script>
                    function toggleConfigForm() {
                        var form = document.getElementById('configForm');
                        if (form.style.display === 'none') {
                            form.style.display = 'block';
                        } else {
                            form.style.display = 'none';
                        }
                    }
                </script>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="browse">
                    <button type="button" class="btn btn-warning small-button text-dark fw-semibold position-relative" onclick="showModal1()">Duyệt thành viên</button>
                </form>
                <form id="disperse-form" method="post" style="display:inline;">
                    <input type="hidden" name="action" value="disperse-clan">
                    <button type="button" class="btn btn-danger small-button fw-semibold position-relative" onclick="showModal('disperse')">Giải tán biệt đội</button>
                </form>

            <?php elseif ($user_rights == 1): ?>
                <button type="button" class="btn btn-warning small-button text-dark fw-semibold me-1" onclick="toggleConfigForm()">Cấu hình biệt đội</button>
                <div id="configForm" style="display:none;">
                    <?php if ($armymem['clan'] > 0 && $armymem['id'] == $clan['master']): ?>
                        <form action="" method="post">
                            Email: <?php echo htmlspecialchars($clan["email"]); ?>
                            <br />
                            Phone: <?php echo htmlspecialchars($clan["phone"]); ?>
                            <br />
                            Giới thiệu (tối đa 200 kí tự):
                            <br />
                            <textarea name="clan-desc" rows="2" cols="40"><?php echo htmlspecialchars($clan["desc"]); ?></textarea>
                            <br />
                            <input class="btn btn-warning small-button text-dark fw-semibold position-relative" type="submit" name="save-desc" value="Cập nhật">
                            <br />

                            <br />
                            <input class="btn btn-danger small-button fw-semibold position-relative" type="submit" name="open-mem" value="Mở thêm thành viên">
                            <br />
                            <br />
                        </form>
                    <?php endif; ?>
                </div>
                <script>
                    function toggleConfigForm() {
                        var form = document.getElementById('configForm');
                        if (form.style.display === 'none') {
                            form.style.display = 'block';
                        } else {
                            form.style.display = 'none';
                        }
                    }
                </script>

                <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="browse">
                    <button type="button" class="btn btn-warning small-button text-dark fw-semibold position-relative" onclick="showModal1()">Duyệt thành viên</button>
                </form>
            <?php elseif ($user_rights == 0): ?>
                <form id="leave-form" method="post" style="display:inline;">
                    <input type="hidden" name="action" value="leave">
                    <button type="button" class="btn btn-danger small-button fw-semibold position-relative" onclick="showModal('leave')">Rời biệt đội</button>
                </form>
            <?php endif; ?>
        </div>
    </center>

    <div id="membersModal" class="modal" style="display:none;">
        <div class="modal-content">
            <div style="text-align: center; margin-top:10px; padding:10px;" class="notiflix-confirm-head">
                <h5 style="color:#32c682;font-size:16px; margin-bottom: 10px;">Danh sách thành viên chờ duyệt</h5>
                <div id="membersList" style="color:#1e1e1e;font-size:14px;">
                    <?php if (count($newMembersClan) > 0): ?>
                        <table class="table fw-semibold mb-0" role="table" width="100%">
                            <thead class="fw-semibold" role="rowgroup">
                                <tr role="row">
                                    <th>Tài Khoản</th>
                                    <th>Duyệt</th>
                                </tr>
                            </thead>
                            <tbody class="table fw-semibold mb-0" role="table" width="100%">
                                <?php foreach ($newMembersClan as $member): ?>
                                    <tr role="row">
                                        <td><?php echo htmlspecialchars($member['user']); ?></td>
                                        <td>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['id']); ?>">
                                                <button type="submit" class="btn btn-warning">Duyệt</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        Không có thành viên nào.
                    <?php endif; ?>
                </div>
            </div>
            <div class="notiflix-confirm-buttons" style="display: flex; justify-content: center;">
                <a onclick="hideModal1()" class="nx-confirm-button-cancel" style="color:#f8f8f8;background:#a9a9a9;font-size:15px;">Đóng</a>
            </div>
        </div>
    </div>
    <script>
    function showModal1() {
        document.getElementById('membersModal').style.display = 'block';
    }

    function hideModal1() {
        document.getElementById('membersModal').style.display = 'none';
    }
    </script>

    <div id="confirmationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div style="text-align: center; margin-top:10px; padding:10px;" class="notiflix-confirm-head">
                <h5 style="color:#32c682;font-size:16px; margin-bottom: 10px;">Xác nhận</h5>
                <div style="color:#1e1e1e;font-size:14px;">Bạn có chắc muốn thực hiện hành động này?</div>
            </div>
            <div class="notiflix-confirm-buttons" style="display: flex; justify-content: space-evenly;">
                <a onclick="confirmAction()" id="NXConfirmButtonOk" class="nx-confirm-button-ok" style="color:#f8f8f8;background:#32c682;font-size:15px;">Có</a>
                <a onclick="hideModal()" id="NXConfirmButtonCancel" class="nx-confirm-button-cancel" style="color:#f8f8f8;background:#a9a9a9;font-size:15px;">Không</a>
            </div>
        </div>
    </div>


    <div class="mt-4">
        <div class="text-center h5">Thành viên</div>
        <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
            <table class="table text-white fw-semibold mb-0" role="table">
                <thead>
                    <tr class="text-start fw-bold text-uppercase gs-0">
                        <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Xạ thủ</th>
                        <th colspan="1" role="columnheader" class="table-sort-desc text-warning" style="cursor: pointer;">Chức vụ</th>
                        <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Đóng góp</th>
                        <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Tham gia</th>
                        <?php if ($user_rights == 2 || $user_rights == 1): ?>
                            <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Hành động</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clan_members as $member): ?>
                        <?php
                        $stmt = $conn->prepare("SELECT NVused, clan FROM armymem WHERE id = ? LIMIT 1");
                        $stmt->bind_param("i", $member['user']);
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();
                        $member_NVused = $result['NVused'];
                        $member_clan = $result['clan'];
                        $stmt->close();
                        if ($member_clan != 0):
                        ?>
                            <tr>
                                <td class="text-start text-white">
                                    <div class="cursor-pointer">
                                        <img src="/images/user/<?php echo htmlspecialchars($member_NVused) ?>.gif" alt="">
                                        <span class="text-warning fw-bold"><?php echo htmlspecialchars($member['user_name']) ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($member['rights'] == 2 ? 'Đội Trưởng' : 'Thành viên'); ?></td>
                                <td><?php echo htmlspecialchars($member['contribute_text']); ?></td>
                                <td><?php echo htmlspecialchars($member['timeJoin']); ?></td>
                                <?php if ($user_rights == 2 || ($user_rights == 1 && $member['user'] != $user_id)): ?>
                                    <td>
                                        <?php if ($user_rights == 2 && $member['rights'] != 2): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="action" value="kick">
                                                <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['user']); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Đuổi</button>
                                            </form>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="action" value="promote">
                                                <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['user']); ?>">
                                                <button type="submit" class="btn btn-warning btn-sm">Nhường chức</button>
                                            </form>
                                        <?php elseif ($user_rights == 1 && $member['user'] == $user_id): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="action" value="leave">
                                                <button type="submit" class="btn btn-danger btn-sm">Rời biệt đội</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<script>
    let actionToPerform = '';
    function showModal(action) {
        actionToPerform = action;
        document.getElementById('confirmationModal').style.display = 'block';
    }
    function hideModal() {
        document.getElementById('confirmationModal').style.display = 'none';
    }
    function confirmAction() {
        if (actionToPerform === 'disperse') {
            document.getElementById('disperse-form').submit();
        } else if (actionToPerform === 'leave') {
            document.getElementById('leave-form').submit();
        }
        hideModal();
    }
</script>

<style>
    input[type="text"],
    input[type="password"],
    input[type="email"] {
        width: 100%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
        margin-bottom: 10px;
    }

    textarea {
        width: 100%;
        height: 100px !important;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
        margin-bottom: 10px;
    }

    button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        background-color: #28a745;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #218838;
    }
</style>
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #f8f8f8;
        margin: 15% auto;
        border: 1px solid #888;
        width: 300px;
        border-radius: 25px;
        animation-duration: 300ms;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .nx-confirm-button-ok {
        cursor: pointer;
        padding: 9px 5px;
        background: #32c682;
        margin: 0 2% 0 0;
        border-radius: 20px !important;
        font-weight: 500;
        text-align: center;
        width: 38%;
        margin-bottom: 10px;
        -webkit-transition: all .25s ease-in-out;
        -o-transition: all .25s ease-in-out;
        transition: all .25s ease-in-out;
    }

    .nx-confirm-button-cancel {
        cursor: pointer;
        padding: 9px 5px;
        background: #32c682;
        margin: 0 2% 0 0;
        border-radius: 20px !important;
        font-weight: 500;
        text-align: center;
        width: 38%;
        margin-bottom: 10px;
        -webkit-transition: all .25s ease-in-out;
        -o-transition: all .25s ease-in-out;
        transition: all .25s ease-in-out;
    }

    .nx-confirm-button-cancel:hover {
        -webkit-box-shadow: inset 0 -60px 5px -5px rgba(0, 0, 0, .25);
        box-shadow: inset 0 -60px 5px -5px rgba(0, 0, 0, .25);
    }

    .nx-confirm-button-ok:hover {
        -webkit-box-shadow: inset 0 -60px 5px -5px rgba(0, 0, 0, .25);
        box-shadow: inset 0 -60px 5px -5px rgba(0, 0, 0, .25);
    }
</style>