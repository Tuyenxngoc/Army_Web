<div class="fw-semibold text-center h5" style="color:#333 !important;">TẢI GAME</div>

<?php
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Kết nối thất bại: " . $conn->connect_error);
}
$query = "SELECT * FROM files";
$result = $conn->query($query);
if ($result->num_rows > 0) {
   $fileTypeMessages = array(
      "jar" => "Dạng 1: Chạy giả lập Java (Độ ổn định cao, nhiều tính năng)",
      "zip" => "Dạng 2: Chạy giả lập Java ( Các phiên bản ghép X Cao )",
      "ipa" => "Dạng 3: Phiên dành cho Iphone, tải về chơi ngay",
      "apk" => "Dạng 4: Phiên bản dành cho đoạn thoại di động",
      "rar" => "Dạng 5: Phiên bản dành cho PC"
   );

   $filesByType = array();
   while ($row = $result->fetch_assoc()) {
      $type = $row["type"];
      if (!isset($filesByType[$type])) {
         $filesByType[$type] = array();
      }
      $filesByType[$type][] = $row;
   }

   foreach ($fileTypeMessages as $type => $message) {
      if (isset($filesByType[$type])) {
         echo '<div class="text-center">';
         echo '<span class="fw-semibold" style="color:#333 !important;">' . $message . ':</span>';
         echo '<div>';
         foreach ($filesByType[$type] as $file) {
            echo '<a href="' . $file["file_path"] . '" class="btn btn-success me-1 mt-1 py-1">' . $file["name"] . '</a>';
         }
         echo '</div>';
         echo '</div>';
      }
   }
} else {
   echo "No files found.";
}
$conn->close();
?>

<style>
   .canchinh {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 60%;
      margin: auto;
   }
</style>



<div class="text-center post-item d-flex align-items-center my-2 canchinh">
   <div class="">
      <div class="h6" style="color:black !important">Hướng dẫn cách cài đặt</div>
      <div style="color:black !important">Bước 1: Tải Microemulator:
         <a  style="color:black !important" href="https://angelchip.net/files/share/AngelChipEmulatorEXE.zip">AngelChipEmulator.zip</a>
      </div>
      <div style="color:black !important">Bước 2: Tải một trong các phiên bản bên trên (Gợi ý: bản 148)</div>
      <div style="color:black !important">Bước 3: Giải nén file AngelChipEmulator.zip đã tải ở bước 1</div>
      <div style="color:black !important">Bước 4: Mở ứng dụng AngelChipEmulator.exe ở thư mục đã giải nén</div>
      <div style="color:black !important">Bước 5: Kéo file game có đuôi .jar đã tải ở trên vào và bấm Start</div>
      <div style="color:black !important">
      Trước khi bấm Start các bạn căn chỉnh lại kích thước sao cho dễ chơi nhất nhé
      </div>
   </div>
</div>