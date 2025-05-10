<?php
    $user = isset($_SESSION['user']) ? $_SESSION['user'] : ['role' => 0];
    $ZaLo = '';
    $sqlZalo = SQL()->query("SELECT value FROM options WHERE `key` = 'linkZalo' LIMIT 1");
    if ($sqlZalo !== false && $sqlZalo->num_rows > 0) {
        $ZaLo = $sqlZalo->fetch_assoc()['value'];
    }
?>
<div class="mb-2">
   <div class="row text-center justify-content-center row-cols-2 row-cols-lg-6 g-2 g-lg-2 mt-1">
      <div class="col">
         <div class="px-2"><a class="position-relative btn btn-menu btn-success w-100 fw-semibold <?php echo($page == "home") ? "active" : "false"; ?>" href="/home">Trang chủ</a></div>
      </div>
      <div class="col">
         <div class="px-2"><a class="position-relative btn btn-menu btn-success w-100 fw-semibold <?php echo($page == "recharge") ? "active" : "false"; ?>" href="javascript:void(0)" onclick="onClickNav('/recharge'); return;">Nạp tiền</a></div>
      </div>
      <div class="col">
         <div class="px-2"><a class="position-relative btn btn-menu btn-success w-100 fw-semibold <?php echo($page == "exchange") ? "active" : "false"; ?>" href="javascript:void(0)" onclick="onClickNav('/exchange'); return;">Đổi lượng</a></div>
      </div>
      <div class="col">
         <div class="px-2"><a class="position-relative btn btn-menu btn-success w-100 fw-semibold" href="<?php echo htmlspecialchars($ZaLo); ?>" target="_blank">Box Zalo</a></div>
      </div>
      <div class="col">
         <div class="px-2"><a class="position-relative btn btn-menu btn-success w-100 fw-semibold false" href="/ranking">Ranking</a></div>
      </div>
      <?php if ($user['role'] == 1): ?>
      <div class="col">
         <div class="px-2"><a class="position-relative btn btn-menu btn-success w-100 fw-semibold false" href="/admin/home">Admin</a></div>
      </div>
      <?php endif; ?>
   </div>
</div>
<script>
    function onClickNav(goto){
        let isLogged = <?php echo isset($isLogged) && $isLogged == true ? "true" : "false"; ?> 
        if(!isLogged){
            $("#modalLogin").modal("show");
        } else {
            window.location.href = goto;
        }
    }
</script>
