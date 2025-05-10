<div class="text-center card">
    <div class="card-body">
        <div class=""><a href="/"><img class="logo" alt="Logo" src="/images/logo.png" style="max-width: 180px;"></a></div>
        <div class="mt-3">
            <?php
            if ($isLogged) {
                $tv = '';
                if ($user['tongnap'] >= 5000000) {
                    $tv = ' - Thành viên Kim Cương';
                } elseif ($user['tongnap'] < 5000000 && $user['tongnap'] >= 2000000) {
                    $tv = ' - Thành viên Vàng';
                } elseif ($user['tongnap'] < 2000000 && $user['tongnap'] >= 1000000) {
                    $tv = ' - Thành viên Bạc';
                }
                echo '
                    <div>
                        <a class="btn btn-success me-2 px-3 py-1" href="/user/profile">' . $user['user'] . ' - ' . number_format($user['balance']) . ' Coin' . $tv . '</a>
                        <a class="btn btn-success me-2 px-3 py-1" href="/logout">Đăng xuất</a>
                    </div>';
                if ($user['active'] != 1) {
                    echo '<div class="mt-2">
                        <small class="text-danger fw-semibold mt-3">Tài khoản của bạn chưa được kích hoạt, click vào phía dưới để kích hoạt.</small>
                        <div class="mt-2">
                            <a data-bs-toggle="modal" data-bs-target="#modalActive" class="mb-3 px-2 py-1 fw-semibold text-secondary bg-danger bg-opacity-25 border border-danger border-opacity-75 rounded-2" style="color: black !important;">Kích hoạt tài khoản</a>
                        </div>
                    </div>';
                }
            } else {
                echo '
                    <a class="mt-3" data-bs-toggle="modal" data-bs-target="#modalLogin">
                        <span class="btn btn-success me-2 px-3 py-1">Đăng nhập</span>
                    </a>
                    <a class="mt-3" data-bs-toggle="modal" data-bs-target="#modalRegister">
                        <span class="btn btn-success me-2 px-3 py-1">Đăng Ký</span>
                    </a>
                ';
            }
            ?>
        </div>
        <div class="mt-3"><a class="btn btn-success px-3 py-1" href="/download">Tải Game</a></div>
    </div>
</div>
