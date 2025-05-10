<?php

//config 

$ip_sv = "localhost";
$dbname_sv = "army2";
$user_sv = "root";
$pass_sv = "Duc2301@!";

//GMT +7

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Create connection

$conn = new mysqli($ip_sv, $user_sv, $pass_sv, $dbname_sv);
    
// Check connection
    
if ($conn->connect_error) {
    
    die("Connection failed: " . $conn->connect_error);
    exit(0);
        
}

$login = false;
$connect = true;
session_name('ADMIN');
session_start();

if ($_GET['c'] == 'logout') {
    unset($_SESSION['user']);
    header('Location: login.php');
}
$login = isset($_SESSION['user']);