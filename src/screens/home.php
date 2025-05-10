<div class="card-title h5" style="color:black;">Bài viết mới</div>
<hr>
<div>
   <?php
   $posts = __select("news_posts");
   if ($posts != false && $posts->num_rows > 0) {
      while ($item = $posts->fetch_assoc()) {
         $conn = new mysqli($servername, $username, $password, $dbname);
         if ($conn->connect_error) {
            die("Kết nối thất bại: " . $conn->connect_error);
         }
         $currentUrl = $_SERVER['REQUEST_URI'];
         $urlParts = explode('/', $currentUrl);
         $slug = $item['slug'];
         $countQuery = "SELECT COUNT(*) AS cmt FROM binhluan WHERE slug = '$slug'";
         $result = mysqli_query($conn, $countQuery);
         if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $cmt = $row['cmt'];
         } else {
            echo "Lỗi truy vấn: " . mysqli_error($conn);
            $cmt = 0;
         }
         $item_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]/post/" . $item['slug'];
         echo '
        <div class="post-item d-flex align-items-center my-2">
           <div class="post-image"><img src="/images/small/1.gif" alt="' . $item['title'] . '"></div>
           <div >
              <a style="color:#0d6efd !important;" class="fw-bold" href="/post/' . $item['slug'] . '">' . $item['title'] . '</a>

              <div style="color:#6c757d!important;" class="text-muted font-weight-bold">Lượt xem: ' . $item['views'] . ', Bình luận: ' . $cmt . '<span class="comments-count" data-href="' . $item_url . '"></span></div>
           </div>
        </div>
        ';
      }
   }
   ?>

</div>

<div class="mt-4">
   <div class="card-title h5" style="color:black;">Giới thiệu</div>
   <hr>
   <div class="fs-6" style="color:black;">
      <p>Mobi Army 2D là game mobile nhập vai chơi theo lượt với phong cách đồ họa sinh động, lối chơi đa dạng và đầy hấp dẫn.</p>
      <p class="text-center"><img src="/images/banner/1.jpg" alt="Thumbnail" class="w-100"></p>
      <p>Trong game người chơi phải tính toán góc bắn và lực bắn đồng thời phải kết hợp với sức gió để đường đi của viên đạn trúng mục tiêu. Với lối chơi đa dạng người chơi có thể chiến đấu với nhau, hoặc lập thành 1 nhóm để tham gia khiêu chiến với Boss.</p>
      <p class="text-center"><img src="/images/banner/2.jpg" alt="Thumbnail" class="w-100"></p>
      <p>Phiên bản Mobi Army 2 với nhiều lớp nhân vật hấp dẫn Rocketer, King Kong, Granos , Tarzan , Chicky , Apache , Gunner , Electrician , Miss 6 với tuyệt chiêu riêng biệt đặc sắc mang đậm dấu ấn cá nhân và xuất xứ của nhân vật, bên cạnh đó với những vật phẩm mới độc đáo như: Đạn vòi rồng, Chuột gắn bom, Tên lửa, Đạn xuyên đất, Sao băng, Mưa đạn, Khoan đất…Cuộc tranh tài của các bạn sẽ càng hấp dẫn hơn, khốc liệt hơn và đầy bất ngờ hơn. Mobi Army 2 còn hấp dẫn hơn với những vùng chiến đấu mới như: vùng băng tuyết, vùng căn cứ thép, những hoang mạc và những đồng cỏ, rừng chết</p>
   </div>
</div>