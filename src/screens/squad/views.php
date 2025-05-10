<?php
require_once(__DIR__ . '/../../../core/configs.php');
if (!isset($_SESSION['user'])) {
    header('Location: /home');
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['user_id'];
$conn = SQL();
$itemsPerPage = 10;
$page = isset($_GET['pageTotal']) ? (int)$_GET['pageTotal'] : 1;
$page = $page > 0 ? $page : 1;
$offset = ($page - 1) * $itemsPerPage;
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';

function getClans($conn, $offset, $limit, $searchTerm)
{
    $query = "SELECT * FROM clan WHERE name LIKE ? LIMIT $offset, $limit";
    $stmt = $conn->prepare($query);
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die("Query failed: " . $stmt->error);
    }
    $clans = [];
    while ($row = $result->fetch_assoc()) {
        $clans[] = $row;
    }
    return $clans;
}

function getTotalClans($conn, $searchTerm)
{
    $query = "SELECT COUNT(*) AS total FROM clan WHERE name LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die("Query failed: " . $stmt->error);
    }
    $row = $result->fetch_assoc();
    return $row['total'];
}

$clans = getClans($conn, $offset, $itemsPerPage, $searchTerm);
$totalClans = getTotalClans($conn, $searchTerm);
$totalPages = ceil($totalClans / $itemsPerPage);
?>

<div>
    <div class="mb-2 d-flex align-items-center justify-content-between">
        <a href="/squad/create" class="btn btn-warning btn-sm text-dark fw-semibold py-1 btn-success" style="font-size: 13px;">Thành lập</a>
        <form method="get" action="">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Tìm kiếm tên đội..." value="<?php echo htmlspecialchars($searchTerm); ?>" style="max-width: 200px; font-size: 13px;">
            <input type="hidden" name="pageTotal" value="<?php echo htmlspecialchars($page); ?>">
        </form>
    </div>
    <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
        <table class="table text-white fw-semibold mb-0" role="table">
            <thead>
                <tr class="text-start fw-bold text-uppercase gs-0">
                    <th colspan="1" role="columnheader" style="cursor: pointer;">Biệt đội</th>
                    <th colspan="1" role="columnheader" style="cursor: pointer;">Cấp độ</th>
                    <th colspan="1" role="columnheader" style="cursor: pointer;">Đội trưởng</th>
                    <th colspan="1" role="columnheader" style="cursor: pointer;">Cúp</th>
                    <th colspan="1" role="columnheader" style="cursor: pointer;">Thành viên</th>
                </tr>
            </thead>
            <tbody class="fw-semibold" role="rowgroup">
                <?php foreach ($clans as $clan): ?>
                    <tr role="row" data-bs-toggle="modal"
                        data-bs-target="#modalClan"
                        data-masters="<?php echo htmlspecialchars($clan['id']); ?>"
                        data-name="<?php echo htmlspecialchars($clan['name']); ?>"
                        data-master="<?php echo htmlspecialchars($clan['masterName']); ?>"
                        data-level="<?php echo htmlspecialchars($clan['level']); ?>"
                        data-cup="<?php echo htmlspecialchars($clan['cup']); ?>"
                        data-members="<?php echo htmlspecialchars($clan['mem']); ?>"
                        data-membersx="<?php echo htmlspecialchars($clan['memMax']); ?>"
                        data-icon="<?php echo htmlspecialchars($clan['icon']); ?>"
                        data-budget="<?php echo (int) $clan['xu']; ?>"
                        data-luong="<?php echo (int) $clan['luong']; ?>"
                        data-desc="<?php echo htmlspecialchars($clan['desc']); ?>"
                        data-creation="<?php echo htmlspecialchars($clan['dateCreat']); ?>">
                        <td>
                            <div class="cursor-pointer">
                                <img src="/images/res/icon/<?php echo htmlspecialchars($clan['icon']); ?>.png" alt="<?php echo htmlspecialchars($clan['name']); ?>">
                                <span class="ms-2 fw-semibold"><?php echo htmlspecialchars($clan['name']); ?></span>
                            </div>
                        </td>
                        <td role="cell">
                            <div><?php echo htmlspecialchars($clan['level']); ?></div>
                        </td>
                        <td role="cell">
                            <div><?php echo htmlspecialchars($clan['masterName']); ?></div>
                        </td>
                        <td role="cell">
                            <div><?php echo htmlspecialchars($clan['cup']); ?></div>
                        </td>
                        <td role="cell">
                            <div><?php echo htmlspecialchars($clan['mem']); ?> / <?php echo htmlspecialchars($clan['memMax']); ?></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start"></div>
        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
            <div>
                <ul class="pagination">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pageTotal=<?php echo max($page - 1, 1); ?>&search=<?php echo urlencode($searchTerm); ?>" style="cursor: pointer;">&lt;</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?pageTotal=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>" style="cursor: pointer;"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pageTotal=<?php echo min($page + 1, $totalPages); ?>&search=<?php echo urlencode($searchTerm); ?>" style="cursor: pointer;">&gt;</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('table').addEventListener('click', function(event) {
            var target = event.target;
            if (target.closest('tr') && target.closest('tr').dataset) {
                var row = target.closest('tr');
                var name = row.dataset.name;
                var master = row.dataset.master;
                var level = row.dataset.level;
                var cup = row.dataset.cup;
                var members = row.dataset.members;
                var membersMax = row.dataset.membersx;
                var icon = row.dataset.icon;
                var budget = parseInt(row.dataset.budget);
                var luong = parseInt(row.dataset.luong);
                var creationDate = row.dataset.creation;
                var desc = row.dataset.desc;
                var masters = row.dataset.masters;

                document.querySelector('#modalClan #clanIcon').src = '/images/res/icon/' + icon + '.png';
                document.querySelector('#modalClan #clanName').textContent = name;
                document.querySelector('#modalClan #clanInfo').textContent = desc;
                // document.querySelector('#modalClan #clanJoin').textContent = masters;
                document.querySelector('#modalClan #clan_id').value = masters;

                document.querySelector('#clanDetailsTable tbody').innerHTML = `
                <tr>
                    <td class="fw-semibold">Đội trưởng</td>
                    <td>${master}</td>
                </tr>
                <tr>
                    <td class="fw-semibold">Thành viên</td>
                    <td>${members} / ${membersMax}</td>
                </tr>
                <tr>
                    <td class="fw-semibold">Cấp độ</td>
                    <td>${level}</td>
                </tr>
                <tr>
                    <td class="fw-semibold">Cúp</td>
                    <td><img src="/images/cup.png" alt="Cup" width="16"> <span class="ms1">${cup}</span></td>
                </tr>
                <tr>
                    <td class="fw-semibold">Ngân sách</td>
                    <td><img src="/images/coin.png" alt="Xu" width="14"> <span class="ms1">${budget.toLocaleString()} xu - ${luong.toLocaleString()} lượng</span></td>
                </tr>
                <tr>
                    <td class="fw-semibold">Thành lập</td>
                    <td>${creationDate}</td>
                </tr>
            `;
            }
        });
    });
</script>