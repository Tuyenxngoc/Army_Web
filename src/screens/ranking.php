<?php
require_once(__DIR__ . '/../../core/configs.php');
$conn = SQL();
$sql = "SELECT `user`.`user`, `armymem`.`id`, `armymem`.`xpMax` FROM `armymem` INNER JOIN `user` ON `armymem`.`id` = `user`.`user_id` ORDER BY `armymem`.`xpMax` DESC LIMIT 10;";
$top_xp = $conn->query($sql);
$sql = "SELECT `user`.`user`, `armymem`.`id`, `armymem`.`xu` FROM `armymem` INNER JOIN `user` ON `armymem`.`id` = `user`.`user_id` ORDER BY `armymem`.`xu` DESC LIMIT 10;";
$top_xu = $conn->query($sql);
$sql = "SELECT `user`.`user`, `armymem`.`id`, `armymem`.`luong` FROM `armymem` INNER JOIN `user` ON `armymem`.`id` = `user`.`user_id` ORDER BY `armymem`.`luong` DESC LIMIT 10;";
$top_luong = $conn->query($sql);
$sql = "SELECT `user`.`user`, `armymem`.`id`, `armymem`.`sk` FROM `armymem` INNER JOIN `user` ON `armymem`.`id` = `user`.`user_id` ORDER BY `armymem`.`sk` DESC LIMIT 10;";
$top_sk = $conn->query($sql);
$sql = "SELECT `user`.`user`, `armymem`.`id`, `armymem`.`dvong` FROM `armymem` INNER JOIN `user` ON `armymem`.`id` = `user`.`user_id` ORDER BY `armymem`.`dvong` DESC LIMIT 10;";
$top_dvong = $conn->query($sql);
$sql = "SELECT `name`, `icon`, `xp`, `level` FROM `clan` ORDER BY `level` DESC LIMIT 10;";
$top_clan = $conn->query($sql);
?>
<div class="bg-content">
   <div class="title">
      <h4>Xếp Hạng Biệt Đội</h4>
   </div>
   <br>
   <div class="mt-2">
      <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
         <table class="table text-white fw-semibold mb-0" role="table">
            <tbody>
               <thead>
                  <tr class="text-start fw-bold text-uppercase gs-0">
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Icon</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Tên</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Cấp</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Kinh nghiệm</th>
                  </tr>
               </thead>
               <?php while ($clan = $top_clan->fetch_assoc()) { ?>
                  <tr role="row">
                     <td role="cell" class="">
                        <img src="/images/res/icon/<?php echo $clan['icon'] ?>.png">
                     </td>
                     <td role="cell" class="">
                        <?php echo $clan['name'] ?>
                     </td>
                     <td role="cell" class="">
                        <?php echo $clan['level'] ?>
                     </td>
                     <td role="cell" class="">
                        <?php echo $clan['xp'] ?>
                     </td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
<div class="bg-content">
   <div class="title">
      <h4>Xếp Hạng Cao Thủ</h4>
   </div>
   <br>
   <div class="mt-2">
      <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
         <table class="table text-white fw-semibold mb-0" role="table">
            <tbody>
               <thead>
                  <tr class="text-start fw-bold text-uppercase gs-0">
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Tên</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Kinh Nghiệm</th>
                  </tr>
               </thead>

            <?php while ($mem = $top_xp->fetch_assoc()) { ?>
               <tr role="row">
                     <td role="cell" class="">
                        <?php echo $mem['user'] ?>
                     </td>
                     <td role="cell" class="">
                        <?php echo $mem['xpMax'] ?>
                     </td>
                  </tr>
            <?php } ?>
         </tbody>
      </table>
   </div>
</div>
</div>
<div class="bg-content">
   <div class="title">
      <h4>Xếp Hạng Sự Kiện</h4>
   </div>
   <br>
   <div class="mt-2">
      <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
         <table class="table text-white fw-semibold mb-0" role="table">
            <tbody>
               <thead>
                  <tr class="text-start fw-bold text-uppercase gs-0">
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Tên</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Điểm sk</th>
                  </tr>
               </thead>
               <?php while ($mem = $top_sk->fetch_assoc()) { ?>
                  <tr role="row">
                     <td role="cell" class="">
                        <?php echo $mem['user'] ?>
                     </td>
                     <td role="cell" class="">
                        <?php echo $mem['sk'] ?>
                     </td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
   </div>
</div>

<div class="bg-content">
   <div class="title">
      <h4>Xếp Hạng Xu</h4>
   </div>
   <br>
   <div class="mt-2">
      <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
         <table class="table text-white fw-semibold mb-0" role="table">
            <tbody>
               <thead>
                  <tr class="text-start fw-bold text-uppercase gs-0">
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Tên</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Xu</th>
                  </tr>
               </thead>
               <?php while ($mem = $top_xu->fetch_assoc()) { ?>
                  <tr role="row">
                     <td role="cell" class="">
                        <?php echo $mem['user'] ?>
                     </td>
                     <td role="cell" class="">
                        <?php echo $mem['xu'] ?>
                     </td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
   </div>
</div>

<div class="bg-content">
   <div class="title">
      <h4>Xếp Hạng Lượng</h4>
   </div>
   <br>
   <div class="mt-2">
      <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
         <table class="table text-white fw-semibold mb-0" role="table">
            <tbody>
               <thead>
                  <tr class="text-start fw-bold text-uppercase gs-0">
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Tên</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Lượng </th>
                  </tr>
               </thead>
               <?php while ($mem = $top_luong->fetch_assoc()) { ?>
                  <tr role="row">
                     <td role="cell" class="">
                        <?php echo $mem['user'] ?>
                     </td>
                     <td role="cell" class="">
                        <?php echo $mem['luong'] ?>
                     </td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
   </div>
</div>

<div class="bg-content">
   <div class="title">
      <h4>Xếp Hạng Danh Vọng</h4>
   </div>
   <br>
   <div class="mt-2">
      <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
         <table class="table text-white fw-semibold mb-0" role="table">
            <tbody>
               <thead>
                  <tr class="text-start fw-bold text-uppercase gs-0">
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Tên</th>
                     <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Danh vọng</th>
                  </tr>
               </thead>
               <?php while ($mem = $top_dvong->fetch_assoc()) { ?>
                  <tr role="row">
                     <td role="cell" class="">
                        <?php echo $mem['user'] ?>
                     </td>
                     <td role="cell" class="">
                        <?php echo $mem['dvong'] ?>
                     </td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
<div class="bg_tree"></div>