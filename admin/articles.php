<?php
define('NP', true);
require(__DIR__ . '/../core/configs.php');

$user = ['role' => 1];
if ($user['role'] != 1) {
    header("Location: /home");
    exit();
}

function generateSlug($title)
{
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

$message = '';
$conn = SQL();

$editPost = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT id, title, content FROM news_posts WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $editPost = $result->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger" role="alert">Bài viết không tồn tại.</div>';
    }
}


if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "DELETE FROM news_posts WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $message = '<div class="alert alert-success" role="alert">Bài viết đã được xóa thành công!</div>';
        header('Location: articles');
        exit();
    } else {
        $message = '<div class="alert alert-danger" role="alert">Có lỗi xảy ra khi xóa bài viết.</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['content'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $slug = generateSlug($title);

        $sql = "UPDATE news_posts SET title = '$title', content = '$content', slug = '$slug' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            $message = '<div class="alert alert-success" role="alert">Bài viết đã được cập nhật thành công!</div>';
            header('Location: articles');
            exit();
        } else {
            $message = '<div class="alert alert-danger" role="alert">Có lỗi xảy ra khi cập nhật bài viết.</div>';
        }
    } elseif (isset($_POST['title']) && isset($_POST['content'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $slug = generateSlug($title);

        $sql = "INSERT INTO news_posts (title, content, views, status, slug) VALUES ('$title', '$content', 0, 'draft', '$slug')";
        if ($conn->query($sql) === TRUE) {
            $message = '<div class="alert alert-success" role="alert">Bài viết đã được thêm thành công!</div>';
            header('Location: articles');
            exit();
        } else {
            $message = '<div class="alert alert-danger" role="alert">Có lỗi xảy ra khi thêm bài viết.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger" role="alert">Dữ liệu form không đầy đủ!</div>';
    }
}

$sql = "SELECT id, title, content, views, status FROM news_posts";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bài Viết</title>
    <script src="https://cdn.tiny.cloud/1/emjf0md4v5hb2rrh2g3loun3x42exzifdgqks9ftq4b7ow1j/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        function initTinyMCE() {
            tinymce.init({
                selector: '#article-content, #edit-article-content',
                plugins: [
                'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
                'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
                'media', 'table', 'emoticons', 'help'
                ],
                toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
                    'forecolor backcolor emoticons | help',
                menu: {
                    file: { title: 'File', items: 'newdocument restoredraft | preview | importword exportpdf exportword | print | deleteallconversations' },
                    edit: { title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall | searchreplace' },
                    view: { title: 'View', items: 'code revisionhistory | visualaid visualchars visualblocks | spellchecker | preview fullscreen | showcomments' },
                    insert: { title: 'Insert', items: 'image link media addcomment pageembed codesample inserttable | math | charmap emoticons hr | pagebreak nonbreaking anchor tableofcontents | insertdatetime' },
                    format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | styles blocks fontfamily fontsize align lineheight | forecolor backcolor | language | removeformat' },
                    tools: { title: 'Tools', items: 'spellchecker spellcheckerlanguage | a11ycheck code wordcount' },
                    table: { title: 'Table', items: 'inserttable | cell row column | advtablesort | tableprops deletetable' },
                    help: { title: 'Help', items: 'help' }
                },
                menubar: 'favs file edit view insert format tools table help',
                content_css: 'css/content.css',
                height: 700
            });
        }

        function showAddArticleForm() {
            var form = document.getElementById('add-article-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            if (form.style.display === 'block') {
                initTinyMCE();
            }
        }

        function showTextareaContent() {
            var content = tinymce.get('article-content').getContent();
            document.getElementById('textarea-content').innerHTML = `<pre>Received Content: ${content}</pre>`;
        }

        function editArticle(id, title, content) {
            document.getElementById('edit-article-form').style.display = 'block';
            document.getElementById('edit-article-id').value = id;
            document.getElementById('edit-article-title').value = title;
            tinymce.get('edit-article-content').setContent(content);
        }


        document.addEventListener('DOMContentLoaded', function() {
            initTinyMCE();
        });
    </script>
    <style>
        #edit-article-form {
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        #add-article-form {
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        #add-article-form textarea {
            display: block;
        }

        #articles {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        button {
            background-color: #007BFF;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
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
            width: calc(100% - 24px);
            padding: 12px;
            margin-bottom: 16px;
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
            table-layout: fixed;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
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

        #add-article-form {
            display: none;
        }
    </style>
</head>

<body>
    <div id="articles" class="section">
        <a href="/admin">
            <h4>Quay Lại</h4>
        </a>
        <?php if ($message) echo $message; ?>
        <h2>Quản lý Bài Viết</h2>
        <button onclick="showAddArticleForm()">Thêm Bài Viết</button>
        <div id="add-article-form">
            <h3>Thêm Bài Viết</h3>
            <form id="add-article-form-element" method="post" onsubmit="showTextareaContent()">
                <label for="article-title">Tiêu Đề:</label>
                <input type="text" id="article-title" name="title" required>
                <label for="article-content">Nội Dung:</label>
                <textarea id="article-content" name="content"></textarea>
                <button type="submit">Lưu</button>
            </form>
        </div>

        <div id="edit-article-form" style="display: <?php echo isset($editPost) ? 'block' : 'none'; ?>;">
            <h3>Sửa Bài Viết</h3>
            <form id="edit-article-form-element" method="post">
                <input type="hidden" id="edit-article-id" name="id" value="<?php echo isset($editPost['id']) ? htmlspecialchars($editPost['id']) : ''; ?>">
                <label for="edit-article-title">Tiêu Đề:</label>
                <input type="text" id="edit-article-title" name="title" value="<?php echo isset($editPost['title']) ? htmlspecialchars($editPost['title']) : ''; ?>" required>
                <label for="edit-article-content">Nội Dung:</label>
                <textarea id="edit-article-content" name="content"><?php echo isset($editPost['content']) ? htmlspecialchars($editPost['content']) : ''; ?></textarea>
                <button type="submit">Cập Nhật</button>
            </form>
        </div>

        <h3>Danh Sách Bài Viết</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu Đề</th>
                    <th>Views</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['title']}</td>
                                <td>{$row['views']}</td>
                                <td>{$row['status']}</td>
                                <td>
                                    <button onclick=\"window.location.href='?action=edit&id={$row['id']}'\">Sửa</button>
                                    <button onclick=\"window.location.href='?action=delete&id={$row['id']}'\">Xóa</button>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Không có bài viết nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>