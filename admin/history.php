<?php
define('NP', true);
require(__DIR__ . '/../core/configs.php');
$post = json_decode(file_get_contents('php://input'), true);

$user = ['role' => 1];
if ($user['role'] != 1) {
    header("Location: /home");
    exit();
}

$conn = SQL();

$sql = "SELECT id, message, amount, received, created_at FROM atm_bank";
$result = $conn->query($sql);
?>

<div id="history" class="section">
    <a href="/admin">
        <h4>Quay Lại</h4>
    </a>

    <h2>Lịch Sử Nạp Tiền</h2>
    <button onclick="showAddHistoryForm()">Thêm Giao Dịch</button>
    <div id="add-history-form" style="display: none;">
        <h3>Thêm Giao Dịch</h3>
        <form>
            <label for="user-id">ID Người Dùng:</label>
            <input type="text" id="user-id" name="user_id" required>
            <label for="amount">Số Tiền:</label>
            <input type="number" id="amount" name="amount" required>
            <label for="date">Ngày:</label>
            <input type="date" id="date" name="date" required>
            <button type="submit">Lưu</button>
        </form>
    </div>
    <h3>Danh Sách Giao Dịch</h3>
    <table>
        <thead>
            <th>ID</th>
            <th>Tài Khoản Nhận</th>
            <th>Số Tiền</th>
            <th>Thực Nhận + Km%</th>
            <th>Thời Gian</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['message']}</td>
                            <td>{$row['amount']}</td>
                            <td>{$row['received']}</td>
                            <td>{$row['created_at']}</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Không có bài viết nào.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<style>
    #articles,
    #history {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    button {
        background-color: #007BFF;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1em;
        transition: background-color 0.3s, transform 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    button:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    button:active {
        background-color: #004494;
        transform: scale(0.98);
    }

    #add-history-form,
    #add-article-form {
        background: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    form label {
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }

    form input,
    form textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1em;
        background-color: #ffffff;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    form input:focus,
    form textarea:focus {
        border-color: #007BFF;
        outline: none;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
    }

    th {
        background: #e9ecef;
        color: #333;
    }

    td {
        background-color: #ffffff;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }
</style>