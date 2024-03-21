<?
    require "inc/init.php";
?>

<?
    require "inc/header.php";
?>

<!-- API lấy tất cả tên thể loại của sách -->
<?
    $url = BOOK_URL . "/get_all_categories.php";
    $response = file_get_contents($url);
    if ($response === false) {
        echo "Lỗi khi gọi API";
    } else {
        // Xử lý dữ liệu nhận được từ API
        $data_categories = json_decode($response, true);
        if ($data_categories) {
        } else {
            // Xử lý lỗi nếu dữ liệu không hợp lệ
            echo "Dữ liệu API không hợp lệ";
        }
    }
?>

<!-- API tạo sách -->
<?
    // Kiểm tra nếu có dữ liệu được gửi đi từ form POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Thu thập dữ liệu từ form
    $title = $_POST["title"];
    $available = $_POST["available"];
    $description = $_POST["description"];
    $category = $_POST["category"];
    $author = $_POST["author"];
    $free = $_POST["free_book"];
    $image = $_POST["image"];
    $access_token = $_COOKIE['access_token'];

    // Chuẩn bị dữ liệu để gửi đến API
    $data = [
        'title' => $title,
        'available' => $available,
        'description' => $description,
        'category_code' => $category,
        'author' => $author,
        'free' => $free,
        'image' => $image
    ];

    $createBookUrl = BOOK_URL . "/create_book.php";
    $ch = curl_init($createBookUrl);
    
    $headers = array(
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Bearer " . $access_token,
    );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);


    // Xử lý kết quả từ API
    if ($httpCode === 200) {
        $object = json_decode($response);
        echo "<script> 
                window.location.href = 'index.php';     
             </script>";
    } else {
        echo "<script> 
        var cmm = JSON.stringify($response); 
                  alert(cmm)      
        </script>";
    }
}
    
?>


<div class="content" id="add_book">
    <div class="addBook justify-content-center">
      <div class="addBook">
        <div class="card">
          <div class="card-header">
            <h3 class="mb-0 text-center">Add Book</h3>
          </div>
          <div class="card-body">
            <form class="row" action="add-book.php" method="POST">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="available">Available</label>
                        <input type="number" class="form-control" id="available" name="available" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                        <?php foreach ($data_categories["categories"] as $category): ?>
                            <option value="<?php echo $category['code']; ?>"><?php echo $category['value']; ?></option>
                        <?php endforeach; ?> 
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="author">Author</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                    <div class="form-group">
                        <label>Free Book</label>
                        <div class="form-check form-check-inline ms-4">
                            <input class="form-check-input" type="radio" name="free_book" id="free_book_0" value="0" required>
                            <label class="form-check-label" for="free_book_0">No</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="free_book" id="free_book_1" value="1" required>
                            <label class="form-check-label" for="free_book_1">Yes</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="image">Image URL</label>
                        <input type="url" class="form-control" id="image" name="image" oninput="displayImage(this.value)">
                    </div>
                    <div class="img_upload form-group">
                        <img id="book-image" src="./uploads/no_image.jpg" alt="Book Image" style="max-width: 80%; height: auto;">
                    </div>
                </div>
              <div class="button-group">
                <button type="submit" class="btn mr-2">Add Book</button>
                <a href="index.php" class="btn btn-cancel">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
    function displayImage(url) {
      var img = document.getElementById('book-image');
      img.src = url || "./uploads/no_image.jpg";
    }
</script>

<?
    require "inc/footer.php";
?>