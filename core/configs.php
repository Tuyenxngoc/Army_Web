<?php
ob_start();
defined('NP') or header('location: /');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/function.php');

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$isMaintained = false;

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];


$list_recharge_price_atm = [
    [
        "amount" => 10000,
        "bonus" => 100 //25

    ],
    [
        "amount" => 50000,
        "bonus" => 100 //25
    ],
    [
        "amount" => 100000,
        "bonus" => 100 //25
    ],
    [
        "amount" => 200000,
        "bonus" => 100 //25
    ],
    [
        "amount" => 500000,
        "bonus" => 100 //25
    ],
    [
        "amount" => 1000000,
        "bonus" => 100 //27
    ],
    [
        "amount" => 2000000,
        "bonus" => 100 //30
    ],
    [
        "amount" => 5000000,
        "bonus" => 100 //36
    ],
    [
        "amount" => 10000000,
        "bonus" => 100 //45
    ],
];


$configNapTien = [
    'atm' => [
        'nganhang' => 'Dạng Háng', //Tên Ngân Hàng
        'chutaikhoan' => 'Đức Cu To', //chủ tài khoản atm mà bạn sử dụng
        'sotaikhoan' => '11223344', //số tài khoản atm bạn sử dụng
        'apikey' => '',
        'matkhau' => ''
    ]
];

$fees = [
    'active' => 10000,
];

$bonusDoiLuong = [
    'bonus' => 0
];

$configDoiLuong = [
    [
        'pCoin' => 10000,
        'luong' => 10000,
    ],
    [
        'pCoin' => 20000,
        'luong' => 20000,
    ],
    [
        'pCoin' => 50000,
        'luong' => 50000,
    ],
    [
        'pCoin' => 100000,
        'luong' => 100000,
    ],
    [
        'pCoin' => 200000,
        'luong' => 200000,
    ],
    [
        'pCoin' => 500000,
        'luong' => 500000,
    ],
    [
        'pCoin' => 1000000,
        'luong' => 1000000,
    ],
    [
        'pCoin' => 2000000,
        'luong' => 2000000,
    ],
    [
        'pCoin' => 5000000,
        'luong' => 5000000,
    ],
];

$bonusDoiXu = [
    'bonus' => 0
];

$configDoiXu = [
    [
        'pCoin' => 10000,
        'xu' => 50000000,
    ],
    [
        'pCoin' => 20000,
        'xu' => 100000000,
    ],
    [
        'pCoin' => 50000,
        'xu' => 250000000,
    ],
    [
        'pCoin' => 100000,
        'xu' => 700000000,
    ],
    [
        'pCoin' => 200000,
        'xu' => 2000000000,
    ]
];

// config nap the dien thoai
$configChargingCard = [
    'partnerID' => "71884690152",
    'partnerKey' => "452a939d11faebc1e370ae6d9cadcdb0",
];

// config bonus nap the
$configBonusCharge = [
    [
        'bn0' => 0,
        'bn1' => 0,
        'bn2' => 0.02,
        'bn3' => 0.02,
        'bn4' => 0.03,
        'bn5' => 0.05
    ],
    [
        'bn0' => 0,
        'bn1' => 0.02,
        'bn2' => 0.04,
        'bn3' => 0.05,
        'bn4' => 0.07,
        'bn5' => 0.1
    ],
    [
        'bn0' => 0.02,
        'bn1' => 0.03,
        'bn2' => 0.05,
        'bn3' => 0.07,
        'bn4' => 0.1,
        'bn5' => 0.13
    ],
    [
        'bn0' => 0.03,
        'bn1' => 0.05,
        'bn2' => 0.07,
        'bn3' => 0.10,
        'bn4' => 0.13,
        'bn5' => 0.15
    ],
];
