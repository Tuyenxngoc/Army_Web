<?php
    define('NP', true);
    require(__DIR__.'/core/configs.php');

    if ($isMaintained) {
        include("maintenance.php");
        return;
    }

    session_start();

    $isLogged = false;
    $user = null;
    
    if (isset($_SESSION['isLogged'])) {
        $isLogged = $_SESSION['isLogged'];
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if ($pageWasRefreshed && isset($_SESSION['user'])) {
            $sql = SQL()->query("SELECT * FROM user WHERE user = '".$_SESSION['user']['user']."' LIMIT 1");
            if ($sql !== false && $sql->num_rows > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $_SESSION['user'] = $row;
                }
            }
        }
    }
    
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
    }

    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    if (isset($page) && $page == 'logout') {
        session_destroy();
        header("Location: /");
        exit();
    }

    $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $title = '';
    $description = '';

    $sqlTitle = SQL()->query("SELECT value FROM options WHERE `key` = 'title_web' LIMIT 1");
    $sqlDescription = SQL()->query("SELECT value FROM options WHERE `key` = 'description' LIMIT 1");

    if ($sqlTitle !== false && $sqlTitle->num_rows > 0) {
        $title = $sqlTitle->fetch_assoc()['value'];
    }

    if ($sqlDescription !== false && $sqlDescription->num_rows > 0) {
        $description = $sqlDescription->fetch_assoc()['value'];
    }

    if ($page == 'logout') {
        session_destroy();
        header("Location: /");
        exit;
    }
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/logo.png" type="image/x-icon" />
    <link rel="shortcut icon" href="/static/images/logo.png" type="image/x-icon" />
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="keywords"
        content="mobi army lậu, army lậu, army 2 lậu, army lau, source army, army2lau free, game lau, armylau hack, mobi army 2 lau, mobi army 3 lau, army free xu, army hack, army full xu, army2lau tk, army lậu tk, army lậu lhd, army lậu atvn, army lậu htt, army lậu tnt, army lậu trungkenbi, army lậu popo, army 2, armylau, army2 lậu, army2lau, army 2 lậu atvn, army2 lậu lhd, army lậu xyz, army lậu wapvip, army 2 gamehub.pro," />
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <script src="/static/js/jquery.min.js"></script>
    <link href="/static/css/main.css" rel="stylesheet">
    <script src="/static/js/toastr.min.js"></script>
    <script src="/static/js/index.js"></script>
    <link href="/static/css/toastr.min.css" rel="stylesheet" />
    <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
    <script src="https://kit.fontawesome.com/1806f43bcd.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script async defer crossorigin="anonymous" src="//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v17.0"
        nonce="tQcugFbH"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.js"></script>
</head>

<body>
    <div id="root">
        <?php include_once('./src/components/loading.php'); ?>
        <div class="background"></div>

        <div id="hello-container" class="d-flex justify-content-center align-items-center min-vh-100 container">
            <div class="text-center p-3 w-100 main-border card" style="background: rgb(4, 58, 52);">
                <div class="card-body">
                    <div class="Typewriter" data-testid="typewriter-wrapper">
                        <span id="typed-text" class="fw-semibold fs-6"></span>
                        <span class="Typewriter__cursor"></span>
                    </div>
                    <div id="button-container"></div>
                </div>
            </div>
        </div>

        <div id="container" class="container d-none">
            <div class="main">
                <?php include_once('./src/components/header.php'); ?>
                <?php include_once('./src/components/navbar.php'); ?>
                <div class="card">
                    <div class="card-body">
                        <?php include_once('./src/routers.php'); ?>
                    </div>
                </div>
                <?php include_once('./src/components/footer.php'); ?>
            </div>
        </div>
    </div>
    <?php include_once('./src/components/clan.php'); ?>
    <?php include_once('./src/components/active.php'); ?>
    <?php include_once('./src/components/forgotpassword.php'); ?>
    <?php include_once('./src/components/login.php'); ?>
    <?php include_once('./src/components/register.php'); ?>
    <script>
    $('[data-bs-dismiss=modal]').on('click', function(e) {
        var $t = $(this),
            target = $t[0].href || $t.data("target") || $t.parents('.modal') || [];

        $(target)
            .find("input,textarea,select")
            .val('')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end();
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <script src="/static/js/popper.min.js"></script>
    <script src="/static/js/bootstrap.min.js"></script>
</body>

</html>