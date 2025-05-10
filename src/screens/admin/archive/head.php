<?php
$title_page = $title_page ? $title_page : ('Army II: Admin');
$meta_key = $meta_key ? $meta_key : ('Admin quản lý');
$meta_desc = $meta_desc ? $meta_desc : ('Army II Admin');
header("Cache-Control: no-store, no-cache, must-revalidate");
header('Content-type: text/html; charset=UTF-8');
echo'﻿﻿<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Final//EN">'.
    "\n" . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="vn"  lang="vn">' .
    "\n" . '<head>' .
    "\n" . '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' .
    "\n" . '<title>' . $title_page . '</title>' .
    "\n" . '<link rel="stylesheet" type="text/css" href="res/style.css" />' .
    "\n" . '<meta name="keywords" content="' . $meta_key. '" />' .
    "\n" . '<meta name="description" content="' . $meta_desc. '" />' .
    "\n" . '</head>' .
    "\n" . '<body>' .
    "\n" . '<div id="header">' .
    "\n" . '<img src="/public/icon/lgw.png" alt="logo" />' .
    "\n" . '<hr />' .
    "\n" . '</div>' . ($login?(
    "\n" . '<hr>' .
    "\n" . '</div>' ) : '' ).
    "\n" . '<div id="notf">' .
    "\n" . ' </div>' 
    ;
?>
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>Mobi Army 2 AdminCpanel</title>
</head>
<?php include('style.php');