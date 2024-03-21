<?php
    require "inc/init.php";
    $books = [];
    $page = null;
    $categoryFilter = null;
    $search_title = null;
    $freeFilter = null;
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


<!-- API  tất cả sách rồi thực hiện phân trang -->
<?php
    function generateCode($value){
        $code = $value[0]. $value[-1].$value[1] .strlen($value) ;
        return strtoupper($code);
    }

    // Kiểm tra nếu có yêu cầu xem sách free hay ko
    if(isset($_GET["free"])) {
        if(!empty($_GET['free'])){ 
            $freeFilter = $_GET['free'];
        }else{
            $freeFilter = '';
       }
    }

    // Kiểm tra xem đã có yêu cầu lọc sách theo danh mục hay chưa
    if(isset($_GET['category'])){
        if(!empty($_GET['category'])){ 
            $categoryFilter = generatecode($_GET['category']);
        }else{
            $categoryFilter = '';
       }
    }

    // Kiểm tra nếu có yêu cầu tìm kiếm
    if(isset($_GET['search'])){ 
        $search_title =$_GET['search'] ;
    }

    if($freeFilter || $search_title || $categoryFilter){
        try {
            // API endpoint
            $api_url = BOOK_URL . "/get_books.php";
            
       
            // Số quyển sách trên mỗi trang
            $booksPerPage = 10;
            

            // Xác định trang hiện tại
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
            } else {
                $page = 1;
            }

            $api_params = "?limit=$booksPerPage&page=$page";

            if($freeFilter){
                $api_params .= "&free=$freeFilter";
            }

            if($search_title){
                $search_title_code = urlencode($search_title);
                $api_params .= "&title=$search_title_code";
            }
            if($categoryFilter){
                $categoryFilter_code = urlencode($categoryFilter);
                $api_params .= "&category_code=$categoryFilter_code";
            }

            $response = file_get_contents($api_url . $api_params);
            $data = json_decode($response, true);

            

            // Dữ liệu trả về từ API
            $books = $data['data'] ?? []; // Sử dụng toán tử null coalescing để xác định giá trị mặc định là mảng rỗng nếu không có dữ liệu trả về
            $totalPages = $data['total_page'] ?? 0; // Sửa key thành 'total_page' thay vì 'total' 4
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }else {
        // API endpoint
        $api_url =BOOK_URL . "/get_books.php";
            
        // Số quyển sách trên mỗi trang
        $booksPerPage = 10;

        

        // Xác định trang hiện tại
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }

        // Gửi yêu cầu GET đến API để lấy dữ liệu sách

        $api_params = "?limit=$booksPerPage&page=$page";
        $response = file_get_contents($api_url . $api_params);
        $data = json_decode($response, true);
        // $offset = ($_GET['page'] - 1) *$_GET['limit'];

        // Dữ liệu trả về từ API
        $books = $data['data']; // Sửa key thành 'data' thay vì 'books'
        $totalPages = $data['total_page']; // Sửa key thành 'total_page' thay vì 'total'   
    }
?>

<? 
    require "./inc/header.php";
?>

<div class="content" id="home">
    <div class="row"> 
        <div class="filter col-md-3">
            <div class="filter-title mb-2">
                <h3 >Filter by Category</h3>
            </div>
            
            <div class="list-group list-categories">
                <button type="button" class="list-group-item list-group-item-action <?php echo ($categoryFilter == '') ? 'active' : ''; ?>" data-category="">All Category</button>
                <?php foreach ($data_categories["categories"] as $category): ?>
                    <button type="button" class="list-group-item list-group-item-action <?php echo ($categoryFilter == generateCode($category['value'])) ? 'active' : ''; ?>" data-category="<?php echo $category['value']; ?>"><?php echo $category['value']; ?></button>
                <?php endforeach; ?> 
            </div>
             
        </div>
        <div class="table-search col-md-9">
            <div class="list-group freeBook-allBook">
                <button type="button" class="list-group-item list-group-item-typeBook list-group-item-action <?php echo ($freeFilter == '1') ? 'active' : ''; ?>" data-free="1">Free Book</button>
                <button type="button" class="list-group-item list-group-item-typeBook list-group-item-action <?php echo ($freeFilter == '') ? 'active' : ''; ?>" data-free="">All Book</button>
            </div>
            <div class="search row mb-3">
                <div class="col">
                    <form method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" id="search-input" placeholder="Search by title">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit" id="search-button">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <table class="table table-bordered table-hover">
                <thead align="center">
                    <tr>
                        <th scope="col" width="5%">#</th>
                        <th scope="col" width="15%">Title</th>
                        <th class="col-mobile" scope="col" width="15%">Category</th>
                        <th class="col-mobile" scope="col" width="40%">Description</th>
                        <th scope="col" width="15%">Author</th>
                        <th scope="col" width="10%">Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = ($page - 1) * $booksPerPage + 1;
                        if(isset($books) && is_array($books)) { // Kiểm tra nếu có dữ liệu sách trả về và là một mảng
                            foreach ($books as $book) {
                    ?>
                                    <tr>
                                        <td align="center"><?php echo $no++; ?></td>
                                        <td align="center"><?php echo $book['title']; ?></td>
                                        <td class="col-mobile" align="center"><?php echo $book['category_value']; ?></td>
                                        <td class="col-mobile"><?php echo $book['description']; ?></td>
                                        <td align="center"><?php echo $book['author']; ?></td>
                                        <td align="center">
                                            <!-- <img src="<?php echo $book['image']; ?>" alt="" width="100" height="150"> -->
                                            <img src="<?php echo ($book['image'] == "") ? "./uploads/no_image.jpg" : $book['image']; ?>" alt="image book" width="100" height="150">
                                            <a class="btn btn-read_more" href="book-detail.php?id=<?=htmlspecialchars($book['id'])?>#book_detail">Detail</a>
                                        </td>
                                    </tr>
                                
                    <?php }
                        } else {
                            echo "<tr><td colspan='6'>No books found</td></tr>"; // Thông báo nếu không có sách nào được tìm thấy
                        }

                        if($books === []) {
                            echo "<tr><td colspan='6'>No books found</td></tr>"; // Thông báo nếu không có sách nào được tìm thấy
                        }
                    ?>
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="pagination justify-content-center">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <?php
                            $currentSearch = isset($_GET['search']) ? $_GET['search'] : '';
                            $currentCategory = isset($_GET['category']) ? $_GET['category'] : ''; 
                            $pageUrl = '';
                            if (!empty($currentSearch)) {
                            $pageUrl .= '&search=' . urlencode($currentSearch);
                            }
    
                            if (!empty($currentCategory)) {
                                $pageUrl .= '&category=' . urlencode($currentCategory);
                            }
                            // Hiển thị nút Previous (Trang trước)
                            if($page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page='.($page - 1). $pageUrl.'">Previous</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
                            }
                            
                            // Hiển thị các trang xung quanh trang hiện tại
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++) {
                                if ($i == $page) {
                                    echo '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
                                } else {
                                    echo '<li class="page-item"><a class="page-link" href="?page='.$i. $pageUrl.'">'.$i.'</a></li>';
                                }
                            }
                            
                            // Hiển thị nút Next (Trang tiếp theo)
                            if($page < $totalPages) {
                                echo '<li class="page-item"><a class="page-link" href="?page='.($page + 1). $pageUrl.'">Next</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">Next</span></li>';
                            }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    // Xử lý sự kiện khi nhấp vào nút danh mục sách
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', event => {
            const category = event.target.getAttribute('data-category');
            window.location.href = 'index.php?category=' + category;
        });
    });
</script>

<script>
    // Xử lý sự kiện khi nhấp vào nút Free Book hoặc All Book
    document.querySelectorAll('.list-group-item-typeBook').forEach(item => {
        item.addEventListener('click', event => {
            const free = event.target.getAttribute('data-free');
            window.location.href = 'index.php?free=' + free;
        });
    });
</script>

<?php
    require "./inc/footer.php";
?>