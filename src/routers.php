<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($page == 'post') {
    include("screens/post.php");
    return;
}
if ($page == 'download') {
    include("screens/download.php");
    return;
}
if ($user == null) {
    include("screens/home.php");
    return;
}

switch ($page) {
    case 'admin':
        if ($tab === 'home') {
            include("screens/admin/index.php");
        } elseif ($tab === 'member') {
            include("screens/admin/member/index.php");
        } elseif ($tab === 'edit') {
            include("screens/admin/member/detail.php");
        } elseif ($tab === 'code') {
            include("screens/admin/giftcode/index.php");
        } elseif ($tab === 'create') {
            include("screens/admin/giftcode/create.php");
        } elseif ($tab === 'edit-giftcode') {
            include("screens/admin/giftcode/detail.php");
        } else {
            include("screens/admin/index.php");
        }
        break;
    case 'squad':
        if ($tab === 'squad') {
            include("screens/squad/views.php");
        } elseif ($tab === 'create') {
            include("screens/squad/create.php");
        } elseif ($tab === 'info') {
            include("screens/squad/info.php");
        }else {
            include("screens/squad/views.php");
        }
        break;
    case 'recharge':
        include("screens/recharge.php");
        break;
    case 'ranking':
        include("screens/ranking.php");
        break;
    case 'exchange':
        include("screens/exchange.php");
        break;
    case 'user':
        include("screens/user.php");
        break;
    case 'home':
        include("screens/home.php");
        break;       
    default:
        include('404.php');
        break;
}
?>
