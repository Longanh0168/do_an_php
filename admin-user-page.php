
<?
require "./inc/init.php";
require "./inc/header.php";


?>
<?
        $userId = $_GET['id'];
        $ProfileUrl = USER_URL . "/get_users.php" . "?id=" . $userId ;
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

    $BRBId = $_GET['id'];
    $AllBRB_url =  BRB_URL . "/get_borrow_return_books.php" . "?user_id=" . $BRBId;

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
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        if ($httpCode === 200) {
            echo "<script> 
                var cmm = JSON.stringify($response); 
                alert(cmm)    
                window.location.href = 'admin-page.php'; 
            </script>";
        } else {  
            echo "<script> 
                var cmm = JSON.stringify($response); 
                alert(cmm)      
            </script>";
        }
    }
    if (isset($_GET['type'])) {
        $borrow_id = $_GET['borrow_id'];
        AcceptRejectBRB($borrow_id);
      }
?>

<div class="content" id="admin-user">
    <div class="user-page row">
        <div class="col-lg-4">
            <div class="user-information">
                <h5 class="text-center mt-3">Thông tin người dùng </h5>
                <div class="m-3">
                    <p>Tên : <? echo $ProfileObject->data[0]->name?></p>
                    <p>Email : <? echo $ProfileObject->data[0]->email?></p>
                    <p>Role : <? echo $ProfileObject->data[0]->role?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-8 table-mobile">
            <table class="table table-brb">
                <thead align="center">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tên sách</th>      
                        <th scope="col">Ngày mượn</th>
                        <th scope="col">Ngày trả</th>
                        <th scope="col">Trạng thái</th>
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
                        <td><?echo $key->borrowed_day?></td>
                        <td><?echo $key->returned_day?></td>
                        <? if($key->status == 0) : 

                     ?>
                        
                        <td>
                            <a  class="btn px-0 py-1 btn-accept" style="width: 100px;" href="?type=1&borrow_id=<? echo $key->id?>&id=<?echo $key->user_id?> ">Chấp nhận</a>
                            <a  class="btn px-0 py-1 btn-cancel" style="width: 100px;" href="?type=2&borrow_id=<? echo $key->id?>&id=<?echo $key->user_id?>">Từ chối</a>
                        </td>
                        
                    <? elseif($key->status == 1 ): ?>
                        <td>
                            <div class="btn px-0 py-1 btn-accept" style="width: 100px;" >Đã chấp nhận</div>
                        </td>
                    <? elseif($key->status == 2) : ?>
                        <td>
                            <div class="btn px-0 py-1 btn-cancel" style="width: 100px;">Đã từ chối</div>
                        </td>
                    <? elseif($key->status == 3 ) : ?>
                        <td>
                        <div href="#" class="btn px-0 py-1 btn-cancel" style="width: 100px; background-color:green; ">Đã trả</div>
                        </td>
                    <? endif ?>
                    <??> 
                    </tr>
                    <? endforeach; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<? require "./inc/footer.php"; ?>