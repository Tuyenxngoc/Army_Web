<?php
   if ($_SESSION['user'] == null) {
      header("Location: /home");
      echo "<script>$('#modalLogin').modal('show');</script>";
      exit();
   }
?>
<div style="background: #209934; border-radius: 1rem; padding: 20px;">
<div class="d-inline d-sm-flex justify-content-center">
   <div class="col-md-8 mb-5 mb-sm-4">
      <div class="d-flex align-items-center justify-content-between">
         <small id="tich-luy" class="fw-semibold">Tích lũy: <?php echo number_format($user['tongnap']) ?></small></div>
   </div>
</div>
<div >
   <div class="fs-5 fw-semibold text-center">Chọn hình thức nạp</div>
   <div class="row text-center justify-content-center row-cols-2 row-cols-lg-5 g-2 g-lg-2 my-1 mb-2">
      <div class="col">
         <a class="w-100 fw-semibold" href="/?page=recharge&tab=atm">
            <div class="recharge-method-item <?php echo ($tab!="atm") ? "active" : "false"; ?>"><img alt="method" src="/images/mb.png" data-pin-no-hover="true" class="nganhang"></div>
         </a>
      </div>
   </div>
</div>
<?php
    if($tab != "atm") include_once('recharge/atm.php');
?>
<script>
   let tongnap = <?php echo ($user['tongnap'] == '' ? 0 : $user['tongnap']) ?>;
   $('#process-charge').attr("style",`width: `+ <?php echo ($user['tongnap']/5000000)*100 ?> +`%`);
   $('#tich-luy').html(`Tích luỹ : `+ tongnap.toLocaleString('it-IT', {style : 'currency', currency : 'VND'}) +``);
</script>
