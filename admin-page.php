<? 
    require "./inc/init.php";
    require "./inc/header.php";
    
    $BASE_URL = "http://localhost/CT06/do_an/api/routes";

?>
    <!-- Lay tat ca lenh muon tra sach -->
    <?
    $AllBRB_url =  BRB_URL . "/get_borrow_return_books.php";

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
    
        echo "<script> 
            var cmm = JSON.stringify($BRBresponse); 
            alert(cmm)      
        </script>";
    }
    ?>
    <!-- Lay tat ca user  -->
    <?
        $AllUserUrl = USER_URL . "/get_users.php";
        $ch = curl_init($AllUserUrl);
        $headers = array(
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Bearer " . $_COOKIE['access_token'] ,
    );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $UserResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    if ($httpCode === 200) {
        $UserObject = json_decode($UserResponse);
        
    } else {
    
        echo "<script> 
            var cmm = JSON.stringify($UserResponse); 
            alert(cmm)      
        </script>";
    }
    ?>

    <!-- thong tin user -->
    <?
        $ProfileUrl = USER_URL . "/get_my_profile.php";
        $ch = curl_init($ProfileUrl);
        $headers = array(
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: Bearer " . $_COOKIE['access_token'] ,
    );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $ProfileResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    if ($httpCode === 200) {
        $ProfileObject = json_decode($ProfileResponse);
    } else {
    
        echo "<script> 
            var cmm = JSON.stringify($ProfileResponse); 
            alert(cmm)      
        </script>";
    }
    ?>

<?
    function AcceptRejectBRB($id){
        $type = $_GET['type'];
        $processUrl = BRB_URL . "/accept_reject_borrow.php?type=" . $type;
        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Bearer " . $_COOKIE['access_token'] ,
        );

        $data = array(
            'borrow_id' => $id,
        );
        $ch = curl_init($processUrl);
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
                window.location.href = 'admin-page.php'; 
            </script>";
        } else {  
            echo "<script> 
                var cmm = JSON.stringify($ProfileResponse); 
                alert(cmm)      
            </script>";
        }
    }
    if (isset($_GET['type'])) {
        $borrow_id = $_GET['borrow_id'];
        AcceptRejectBRB($borrow_id);
      }
?>

<?
    function BlockUser($id){
        $processUrl = USER_URL . "/ban_user.php";
        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Authorization: Bearer " . $_COOKIE['access_token'] ,
        );

        $data = array(
            'user_id' => $id,
        );
        $ch = curl_init($processUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $BlockResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        if ($httpCode === 200) {
            echo "<script> 
                var cmm = JSON.stringify($BlockResponse); 
                alert(cmm)    
                window.location.href = 'admin-page.php'; 
            </script>";
        } else {  
            echo "<script> 
                var cmm = JSON.stringify($BlockResponse); 
                alert(cmm)      
            </script>";
        }
    }
    if (isset($_GET['block'])) {
        $block_id = $_GET['id'];
        BlockUser($block_id);
      }
?>

<div class="content" id="admin">
    <div class="admin-page row"> 
        <div class="col-lg-12">
            <div class="user-information">
                <h5 class="text-center mt-3">Thông tin quản trị viên</h5>
                <div class="m-3">
                    <p>Tên : <? echo $ProfileObject->data->name?></p>
                    <p>Email : <? echo $ProfileObject->data->email?></p>
                    <p>Role : <? echo $ProfileObject->data->role?></p>
                    
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div id="user-manager" class="mt-4">
                <table class="table">
                    <thead align="center">
                        <tr>
                            <th scope="col" align="center">ID</th>
                            <th scope="col" align="center">Tên người dùng</th>
                            <th scope="col" align="center">Email</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody align="center">
                    <? if(isset($UserObject->data) ) : ?>    
                        <? foreach ($UserObject->data as $key ) :?>
                                <tr>
                                    <td><? echo $key->id  ?></td>
                                    <td><? echo $key->name  ?></td>
                                    <td><? echo $key->email  ?></td>
                                    <td>
                                        <a href="admin-user-page.php?id=<? echo $key->id ?>#admin-user" class="btn px-0 py-1 btn-accept" style="width: 100px;">Chi tiết</a>
                                        <? if ($key->isBanned == 00) : ?>
                                            <a href="?block=true&id=<? echo $key->id ?>" class="btn px-0 py-1 btn-accept" style="width: 100px;">Chặn</a>

                                        <?else : ?>
                                            <div disable href="?block=false&id=<? echo $key->id ?>" class="btn px-0 py-1 btn-cancel" style="width: 100px;">Đã chặn</div>
                                        <? endif; ?>     
                                        </td>
                                </tr>
                        <? endforeach; ?>
                    <? else : ?>
                        <h5> Không có User.</h5>
                    <? endif; ?>        
                    </tbody>
                </table>
            </div>
            
            <div id="borrow-return" style="display: block;">
                <table class="table table-brb" >
                    <thead align="center">
                        <tr>
                            <th scope="col">ID Phiếu</th>
                            <th scope="col">Tên sách</th>
                            <th scope="col">ID sách</th>
                            <th scope="col">Tên người dùng</th>
                            <th scope="col">ID người dùng</th>
                            <th scope="col">Ngày mượn</th>
                            <th scope="col">Ngày trả</th>
                            <th scope="col" style="width: 222px;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                    <? if(!isset($BRBobject->data)) {
                            echo " <h5> Không có phiếu mượn.</h5>";
                            return;
                        } else { ?>
                        <? foreach ($BRBobject->data as $key ) :?>
                            
                        <tr>
                            <td><? echo $key->id  ?></td>
                            <td><a href="book-detail.php?id=<? echo $key->book_id?>#book_detail"><? echo $key->book_title ?></a></td>
                            <td><? echo $key->book_id ?></td>

                            <td><? echo $key->user_name  ?></td>
                            <td><? echo $key->user_id ?></td>
                            <td><?echo $key->borrowed_day?></td>
                            <td><?echo $key->returned_day?></td>
                            <? if($key->status == 0) : 

                        ?>
                            <td>
                                <a  class="btn px-0 py-1 btn-accept" style="width: 100px;" href="?type=1&borrow_id=<? echo $key->id?>">Chấp nhận</a>
                                <a  class="btn px-0 py-1 btn-cancel" style="width: 100px;" href="?type=2&borrow_id=<? echo $key->id?>">Từ chối</a>
                            </td>
                        <? elseif($key->status == 1 ): ?>
                            <td>
                                <div class="btn px-0 py-1 btn-accept btn-disabled" style="width: 100px;">Đã chấp nhận</div>
                            </td>
                        <? elseif($key->status == 2) : ?>
                            <td>
                                <div class="btn px-0 py-1 btn-cancel btn-disabled" style="width: 100px;">Đã từ chối</div>
                            </td>
                        <? elseif($key->status == 3 ) : ?>
                            <td>
                            <div class="btn px-0 py-1 btn-cancel btn-disabled" style="width: 100px; background-color:green; ">Đã trả</div>
                            </td>
                        <? endif ?>
                        <??> 
                        </tr>
                        <? endforeach; }?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    


<? require "./inc/footer.php";?>