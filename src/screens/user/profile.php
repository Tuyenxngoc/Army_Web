<?php
if (!isset($_SESSION['user'])) {
   header('Location: /login');
   exit;
}
$user_id = isset($_SESSION['user']['user_id']) ? (int)$_SESSION['user']['user_id'] : null;

if (!$user_id) {
   die('User ID is not defined.');
}

$conn = SQL();

$query = "SELECT xu, luong, request_clan, NVused, dvong, vip_level FROM armymem WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userDetails = $result->fetch_assoc();
$stmt->close();

$clanId = $userDetails['request_clan'];
$clanQuery = "SELECT name FROM clan WHERE id = ?";
$clanStmt = $conn->prepare($clanQuery);
$clanStmt->bind_param("i", $clanId);
$clanStmt->execute();
$clanResult = $clanStmt->get_result();
$clanDetails = $clanResult->fetch_assoc();
$clanName = isset($clanDetails['name']) ? $clanDetails['name'] : 'Trống';
$clanStmt->close();

$nhanvatId = $userDetails['NVused'];
$nhanvatQuery = "SELECT name FROM nhanvat WHERE nhanvat_id = ?";
$nhanvatStmt = $conn->prepare($nhanvatQuery);
$nhanvatStmt->bind_param("i", $nhanvatId);
$nhanvatStmt->execute();
$nhanvatResult = $nhanvatStmt->get_result();
$nhanvatDetails = $nhanvatResult->fetch_assoc();
$nhanvatName = isset($nhanvatDetails['name']) ? $nhanvatDetails['name'] : 'Trống';
$nhanvatStmt->close();

$conn->close();

$createdCharacter = false;
$armymem = array();

$conn = SQL();
$conn->query("SET NAMES utf8");

$stmt = $conn->prepare("SELECT * FROM `user` WHERE `user_id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM `armymem` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$armymem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!empty($armymem)) {
   $createdCharacter = true;
}

if ($createdCharacter) {
   $nameCharacter = array("Gunner", "Miss 6", "Electrician", "King Kong", "Rocketer", "Granos", "Chicky", "Tarzan", "Apache", "Magenta");
   $nvstt = $armymem['sttnhanvat'];
   $characterPurchased = array();
   $ruongTB = json_decode($armymem['ruongTrangBi'], true);
   $len = count($ruongTB);
   $info = array();
   for ($i = 0; $i < 10; $i++) {
      $characterPurchased[$i] = ($nvstt & 1) > 0;
      $info[$i] = json_decode($armymem['NV' . ($i + 1)], true);
      $nvstt = $nvstt / 2;
   }
}
?>

<div class="mt-2">
   <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">
      <table class="table text-white fw-semibold mb-0" role="table">
         <thead>
            <tr class="text-start fw-bold text-uppercase gs-0">
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Menu</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Thông Tin</th>

            </tr>
         </thead>
         <tbody class="fw-semibold" role="rowgroup">
            <tr role="row">
               <td role="cell" class="">
                  <div class="cursor-pointer">
                     <span class="ms-2 fw-semibold">User_Id</span>
                  </div>
               </td>
               <td role="cell" class="">
                  <div><?php echo isset($user['user_id']) ? $user['user_id'] : 'Trống'; ?></div>
               </td>
            </tr>
            <tr role="row">
               <td role="cell" class="">
                  <div class="cursor-pointer">
                     <span class="ms-2 fw-semibold">Tài khoản</span>
                  </div>
               </td>
               <td role="cell" class="">
                  <div><?php echo isset($user['user']) ? $user['user'] : 'Trống'; ?></div>
               </td>
            </tr>
            <tr role="row">
               <td role="cell" class="">
                  <div class="cursor-pointer">
                     <span class="ms-2 fw-semibold">Xu</span>
                  </div>
               </td>
               <td role="cell" class="">
                  <div><?php echo isset($userDetails['xu']) ? $userDetails['xu'] : 'Trống'; ?></div>
               </td>
            </tr>
            <tr role="row">
               <td role="cell" class="">
                  <div class="cursor-pointer">
                     <span class="ms-2 fw-semibold">Lượng</span>
                  </div>
               </td>
               <td role="cell" class="">
                  <div><?php echo isset($userDetails['luong']) ? $userDetails['luong'] : 'Trống'; ?></div>
               </td>
            </tr>
            <tr role="row">
               <td role="cell" class="">
                  <div class="cursor-pointer">
                     <span class="ms-2 fw-semibold">Danh vọng</span>
                  </div>
               </td>
               <td role="cell" class="">
                  <div><?php echo isset($userDetails['dvong']) ? $userDetails['dvong'] : 'Trống'; ?></div>
               </td>
            </tr>
            <tr role="row">
               <td role="cell" class="">
                  <div class="cursor-pointer">
                     <span class="ms-2 fw-semibold">Tiền</span>
                  </div>
               </td>
               <td role="cell" class="">
                  <div><?php echo isset($user['tongnap']) ? $user['tongnap'] : 'Trống'; ?></div>
               </td>
            </tr>
            <td role="cell" class="">
               <div class="cursor-pointer">
                  <span class="ms-2 fw-semibold">Cấp Vip</span>
               </div>
            </td>
            <td role="cell" class="">
               <div><?php echo isset($userDetails['vip_level']) ? $userDetails['vip_level'] : 'Trống'; ?></div>
            </td>
            </tr>
            <td role="cell" class="">
               <div class="cursor-pointer">
                  <span class="ms-2 fw-semibold">Email</span>
               </div>
            </td>
            <td role="cell" class="">
               <div><?php echo isset($user['email']) ? $user['email'] : 'Trống'; ?></div>
            </td>
            </tr>
            <td role="cell" class="">
               <div class="cursor-pointer">
                  <span class="ms-2 fw-semibold">Mật Khẩu Cấp 2</span>
               </div>
            </td>
            <td role="cell" class="">
               <div><?php echo isset($user['password2']) ? $user['password2'] : 'Trống'; ?></div>
            </td>
            </tr>
         </tbody>
      </table>
   </div>
</div>

<hr>

<h6 class="fw-semibold">Nhân Vật</h6>

<div class="mt-2">
   <div class="table-responsive mb-4" style="background: rgb(4, 58, 52); border-radius: 1rem;">

      <table class="table text-white fw-semibold mb-0" role="table">
         <thead>
            <tr class="text-start fw-bold text-uppercase gs-0">
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Nhân Vật</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Cấp</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Kinh Nghiệm</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Sinh lực</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Sức mạnh</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Phòng thủ</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">May mắn</th>
               <th colspan="1" role="columnheader" class="" style="cursor: pointer;">Đồng đội</th>
            </tr>
         </thead>

         <tbody class="fw-semibold" role="rowgroup">
            <?php for ($i = 0; $i < 10; $i++) {
               if (!$characterPurchased[$i]) {
                  continue;
               }
               $nextXP = 500 * $info[$i]['lever'] * ($info[$i]['lever'] + 1);
               $leverPercen = $info[$i]['xp'] * 100 / $nextXP;
               $ability = array(0, 0, 0, 0, 0);
               $invAdd = array(0, 0, 0, 0, 0);
               $percenAdd = array(0, 0, 0, 0, 0);

               $NVData = $conn->query("SELECT `sat_thuong` FROM `nhanvat` WHERE `nhanvat_id` = " . ($i + 1) . " LIMIT 1;")->fetch_assoc();
               $nv_st = $NVData['sat_thuong'];

               $dataEquip = $info[$i]['data'];
               for ($c = 0; $c < 6; $c++) {
                  if ($dataEquip[$c] < 0 || $dataEquip[$c] > $len) {
                     continue;
                  }
                  $eq = $ruongTB[$dataEquip[$c]];
                  for ($d = 0; $d < 5; $d++) {
                     $invAdd[$d] += $eq['invAdd'][$d];
                     $percenAdd[$d] += $eq['percenAdd'][$d];
                  }
               }

               $ability[0] = 1000 + $info[$i]['pointAdd'][0] * 10 + $invAdd[0] * 10;
               $ability[0] += (1000 + $info[$i]['pointAdd'][0]) * $percenAdd[0] / 100;
               $ability[1] = $nv_st * (100 + ($invAdd[1] + $info[$i]['pointAdd'][1]) / 3 + $percenAdd[1]) / 100;
               $ability[2] = ($invAdd[2] + $info[$i]['pointAdd'][2]) * 10;
               $ability[2] += $ability[2] * $percenAdd[2] / 100;
               $ability[3] = ($invAdd[3] + $info[$i]['pointAdd'][3]) * 10;
               $ability[3] += $ability[3] * $percenAdd[3] / 100;
               $ability[4] = ($invAdd[4] + $info[$i]['pointAdd'][4]) * 10;
               $ability[4] += $ability[4] * $percenAdd[4] / 100;
            ?>
               <tr role="row">
                  <td role="cell" class="">
                     <div class="cursor-pointer">
                        <span class="ms-2 fw-semibold"> <?php echo $nameCharacter[$i] . ' <a onclick="view(' . $user_id . ', ' . $i . ')" style="color:red"></a>' ?></span>
                     </div>
                  </td>
                  <td role="cell" class="">
                     <div>
                        <?php echo $info[$i]['lever'] ?></span>
                     </div>
                  </td>
                  <td role="cell" class="">
                     <div>
                        <?php echo $info[$i]['xp'] . '/' . $nextXP . ' (' . (int) $leverPercen . '%)'; ?>
                     </div>
                  </td>
                  <td role="cell" class="">
                     <div>
                        <?php echo (int) $ability[0] ?>
                     </div>
                  </td>
                  <td role="cell" class="">
                     <div>
                        <?php echo (int) $ability[1] ?>
                     </div>
                  </td>
                  <td role="cell" class="">
                     <div>
                        <?php echo (int) $ability[2] ?>

                     </div>
                  </td>
                  <td role="cell" class="">
                     <div>
                        <?php echo (int) $ability[3] ?>

                     </div>
                  </td>
                  <td role="cell" class="">
                     <div>
                        <?php echo (int) $ability[4] ?>
                     </div>
                  </td>
               <?php } ?>
               </tr>
         </tbody>
      </table>
   </div>
</div>