<?php
require "inc/init.php";


if (isset($_COOKIE['role'])) {
    $role = $_COOKIE['role'];
}


require "./inc/header.php";

// Lấy thông tin sách theo ID
$id = $_GET['id'];
try {
    $url = BOOK_URL . "/get_book_by_id.php";
    $api_params = "?id=$id";
    $response = file_get_contents($url . $api_params);
    if ($response === false) {
        echo "Lỗi khi gọi API";
    } else {
        $data = json_decode($response, true);
        if ($data) {
            $book = $data['data'];
        } else {
            echo "Dữ liệu API không hợp lệ";
        }
    }
} catch (PDOException $e) {
    return $e->getMessage();
}

// gọi API lấy tấc cả sách
$api_url =BOOK_URL . "/get_books.php?limit=99&page=1";

// Gửi yêu cầu GET đến API để lấy dữ liệu sách
$response = file_get_contents($api_url);
$databooks = json_decode($response, true);
// Dữ liệu trả về từ API
$books = $databooks['data']; // Sửa key thành 'data' thay vì 'books'


// Xử lý xóa sách bằng cURL
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Thu thập id của sách cần xóa
    $idToDelete = $_GET["id"];
    $access_token = $_COOKIE['access_token'];

    // Chuẩn bị dữ liệu để gửi đến API
    $deleteBookUrl = BOOK_URL . "/delete_book.php?id=" . $idToDelete;

    // Khởi tạo cURL handle
    $ch = curl_init($deleteBookUrl);

    // Thiết lập các tùy chọn cURL
    $headers = array(
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Bearer " . $access_token,
        
    );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('id' => $idToDelete))); // Dữ liệu cần xóa

    // Thực thi cURL và lưu kết quả
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Đóng cURL handle
    curl_close($ch);

    // Xử lý kết quả từ API
    if ($httpCode === 200) {
        // Xóa thành công, có thể redirect hoặc hiển thị thông báo tùy ý
        echo "<script>
        var cmm = JSON.stringify($response); 
        alert(cmm) 
        window.location.href = 'index.php'; 
            </script>";
        exit();
    } else {
        // Xóa không thành công, hiển thị thông báo lỗi
        echo "Xóa sách không thành công";
    }
}
?>

    <? if(isset($_COOKIE['access_token'])): ?>
        <?
            $AllBRB_url = BRB_URL . "/get_my_borrow_return_books.php";

            $ch = curl_init($AllBRB_url);
            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Bearer " . $_COOKIE['access_token'] ,
            );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $BRBresponse = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            if ($httpCode === 200) {
                $BRBobject = json_decode($BRBresponse);

            } else {
            }
        ?>
    <? else: ?>
    <? endif; ?>    
        

<?
    function borrowBook ($id) {

        $Borrow_url = BRB_URL . "/create_borrow_book.php";
        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Bearer " . $_COOKIE['access_token'] ,
        );

        $data = array(
            'book_id' => $id,
        );
        $ch = curl_init($Borrow_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $ProfileResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        if ($httpCode === 200) {
            echo "<script> 
                var cmm = JSON.stringify($ProfileResponse); 
                alert(cmm)
                window.location.href = 'book-detail.php?id=$id';     
            </script>";
        } else {  
            echo "<script> 
                var cmm = JSON.stringify($ProfileResponse); 
                alert(cmm)      
            </script>";
        }
    }
    if(isset($_GET['borrow'])){
        $bookId = $_GET['id'];
        borrowBook($bookId);
    };
?>

<div class="content" id="book_detail">
    <div class="book-detail row mb-5">
        <div class="book-detail_cover col-md-4">
            <div class="book-detail_img row">
                <img src="<?php echo ($book['image'] == "") ? "./uploads/no_image.jpg" : $book['image']; ?>" alt="image book" >
            </div>
            <div class="book-detail_category row">
                <h4>Category: <?php echo $book['category_value']; ?></h4>
            </div>
        </div>
        <div class="book-detail_info col-md-8">
            <div class="book-detail_title row">
               <h3><?php echo $book['title']; ?></h3>
            </div>
            <hr>
            <div class="book-detail_desc row">
                <p>Description: <?php echo $book['description']; ?></p>
                <h5>Author: <?php echo $book['author']; ?></h5>    
                <h5>Free Book: <?php echo ($book['free'] == 1) ? 'Yes' : 'No' ; ?></h5>    
            </div>
            <hr>
            <div class="button-group">
                <div class="book-detail_btn col">
                    <? if(isset($_COOKIE['role'])) : ?>
                        <?if($role == "AD") : ?>
                            <button class="btn"><a href="https://drive.google.com/file/d/1kY5nMGIkaTB2yh0DeB39gOzESkBzL55L/view">Read</a></button>
                            <button class="btn"><a href="update-book.php?id=<?=htmlspecialchars($book['id'])?>#update_book">Update</a></button>
                            <form id="deleteForm" method="post">
                                <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="btn" name="delete">Delete</button>
                            </form>
                            <a href="index.php#home" class="btn btn-cancel">Cancel</a>
                        <? elseif($role == "UR") : ?>
                            <?php
                                $cancelButtonDisplayed = false;
                                if (isset($BRBobject->data)) {
                                    foreach ($BRBobject->data as $key) {
                                        if ($key->book_id == $book['id']) {
                                            if ($key->status == 0) {
                                                ?>
                                                <a href="" class="btn">Chờ duyệt</a>
                                                <a href="index.php#home" class="btn btn-cancel">Cancel</a>
                                                <?php $cancelButtonDisplayed = true;
                                            } elseif (!$cancelButtonDisplayed && $key->status == 1) {
                                                ?>
                                                <button class="btn"><a href="https://drive.google.com/file/d/1kY5nMGIkaTB2yh0DeB39gOzESkBzL55L/view">Read</a></button>
                                                <a href="index.php#home" class="btn btn-cancel">Cancel</a>
                                                <?php $cancelButtonDisplayed = true;
                                            } elseif (!$cancelButtonDisplayed && in_array($key->status, [2, 3])) {
                                                ?>
                                                <button class="btn"><a href="./book-detail.php?id=<?= htmlspecialchars($book['id']) ?>&borrow=true">Borrow</a></button>
                                                <a href="index.php#home" class="btn btn-cancel">Cancel</a>
                                                <?php $cancelButtonDisplayed = true;
                                            }
                                        }
                                    }
                                    if($book['free'] == 1){
                                        ?>
                                        <button class="btn"><a href="https://drive.google.com/file/d/1kY5nMGIkaTB2yh0DeB39gOzESkBzL55L/view">Read</a></button>
                                        <a href="index.php#home" class="btn btn-cancel">Cancel</a> 
                                        <? $cancelButtonDisplayed = true;
                                    }
                                    if (!$cancelButtonDisplayed) {
                                        ?>
                                        <button class="btn"><a href="./book-detail.php?id=<?= htmlspecialchars($book['id']) ?>&borrow=true">Borrow</a></button>
                                        <a href="index.php#home" class="btn btn-cancel">Cancel</a>
                                        <?php
                                    }
                                }else {
                                    ?>
                                    <button class="btn"><a href="./book-detail.php?id=<?= htmlspecialchars($book['id']) ?>&borrow=true">Borrow</a></button>
                                    <a href="index.php#home" class="btn btn-cancel">Cancel</a>
                                    <?php $cancelButtonDisplayed = true;
                                }
                            ?>
                        <? endif; ?>
                    <?elseif(!isset($_COOKIE['role']) && $book['free'] == 1) :?>   
                        <button class="btn"><a href="https://drive.google.com/file/d/1kY5nMGIkaTB2yh0DeB39gOzESkBzL55L/view">Read</a></button>
                        <a href="index.php#home" class="btn btn-cancel">Cancel</a> 
                    <?else: ?>
                        <a href="login.php#login" class="btn">Login to continue</a>
                    <? endif; ?>    
                </div>
            </div>
        </div>
    </div>

    <div class="book-suggest">
        <h2>Books in the Same Category</h2>
        <div class="book-suggest_list">
            <?php
                if(isset($books) && is_array($books)){
                    shuffle($books);
                    foreach ($books as $b){
                        if($b['id'] != $book['id']){
            ?>                
                            <div class="book-suggest_item">
                                <a href="book-detail.php?id=<?php echo htmlspecialchars($b['id']); ?>#book_detail"><img src="<?php echo $b['image']; ?>" alt="image book" width="200"></a>
                            </div>
            <?php
                        }
                    }
                } else {
                    echo "<h5>No books in the same category.</h5>";
                }
            ?>
        </div>
    </div>
</div>

<?php
require "./inc/footer.php";
?>
