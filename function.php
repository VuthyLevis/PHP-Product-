<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" 
integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" 
crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" 
crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php
include 'connection.php'; 

function adduserName(){
global $connection;
if(isset($_POST['user_btn'])){
   // ហាមប្រើប្រាស់ MD5 
   // លក្ខណៈរបៀបនេះ 
   //  Example : 
   // $password = md5('password');
   // ការធ្វើបែបនេះគឺមិនត្រឹមត្រូវទេ នោះវានឹងមិនចាប់ទិន្នន័យដែលយើង Input
   // ចេញពីតម្លៃដែលយើង input នោះទេវានឹងចេញតម្លៃ​ដែលយើងដាក់នៅក្នុងវង់ក្រចកប៉ុណ្ណោះ 
   $password = md5($_POST['password']);
   $username = $_POST['username'];
   $email = $_POST['email'];
   $image = upload_image('image');
   $sql_add_user = "INSERT INTO `db_admin`(`id`,`username`, `email`, `password`, `image`) 
                     VALUES (null,'$username','$email','$password','$image')";
                  $result = $connection -> query($sql_add_user);
                  if($result){
                     header('location:log-out.php');
                  }
   }
}
// WHERE ប្រើប្រាស់សម្រាប់ Filter យកតែរបស់មួយនឹង 
// Example : WHERE `name` AND `password` មានន័យថាយកតែ Name និង Password  តែប៉ុណ្ណោះ
adduserName();
function user_login(){
   session_start();
   global $connection;
   if(isset($_POST['btn_login'])){
      $name_email = $_POST['username'];
      $password = md5($_POST['password']);
      if(!empty($name_email) && !empty($password)){
         $sql_user_login = "SELECT * FROM `db_admin` WHERE (`username` = '$name_email' OR `email` = '$name_email') AND `password` ='$password' ";
         $result_login   = $connection -> query($sql_user_login);
         if($result_login){
            // SESSION ប្រៀបបានដូចទៅនឹងសោរផ្ទះអញ្ចឹង​ ប្រសិនអត់មាន SESSION នឹងទេនោះយើងមិនអាចចូលវាបានទេ ។ 
            $row = mysqli_fetch_assoc($result_login);
            $_SESSION['id'] = $row['id'];
            $_SESSION['password'] = $row['password'];
            $_SESSION['username'] = $row['username'];
            header('location:index.php');
         } 
      }

   }

}
user_login();
function insert_image_product(){
   global $connection;
   if(isset($_POST['btn_add_product'])){
      $name_product = $_POST['product_name'];
      $image_product =  upload_image_product('img_product');
      $price_product = $_POST['price_product'];
      $quantity_product = $_POST['quantity_name'];
      $brand_product = $_POST['brand_product'];
      $category_product = $_POST['product_category'];
      if(!empty($name_product) && !empty($image_product) && !empty($price_product) && !empty($brand_product) && !empty($category_product)){
         $sql_insert_product = " INSERT INTO `db_product`(`name`, `quantity`, `image`, `price`, `category`, `brand`) 
                                 VALUES ('$name_product','$quantity_product','$image_product','$price_product','$category_product','$brand_product')
                                 ";
                                 $result_insert_product = $connection -> query($sql_insert_product);
                                 if($result_insert_product){
                                   echo '
                                    <script>
                                $(document).ready(function(){
                                    swal({
                                        title: "Success",
                                        text: "Your Product Has Been Upload Going To DataBase",
                                        icon: "success",
                                        button: "Confirm",
                                    });
                                })
                            </script>';
                                 
                                 }
      }
   }
}
insert_image_product();
function show_product(){
   global $connection;
   $sql_show_product = "SELECT * FROM `db_product` WHERE 1 ORDER BY `id` ASC ";
   $result_show_product = $connection -> query($sql_show_product);
   
   while($row = mysqli_fetch_assoc($result_show_product)){
      echo '<tr>
                                    
                                  <td scope="row"><b>'.$row['id'].'</b></td>
                                    <td style="font-size: 15px;">
                                    <b>'.$row['name'].'</b>
                                    </td>
                                    <td>
                            <img src="./img/Product-img/'.$row['image'].'" width="100px">
                                    </td>
                                    <td><b>'.$row['category'].'</b></td>
                                    <td><b>'.$row['brand'].'</b></td>
                                    <td><b>'.$row['price'].'</b></td>
                                    <!-- <td>16:00, 12 NOV 2018</td> -->
                                      <td>
                        <button type="button" style="width: 40px; height: 40px; border-radius: 10px;" class="btn btn-outline-danger mx-2 " name="btn_del">
                        <i style="display: flex; justify-content: center; align-items: center;" class="far fa-trash-alt tm-product-delete-icon"></i>
                      </button>
                       <a href="edit-product.php?id='.$row['id'].'" style="width: 50px; height: 50px; border-radius: 10px;"  class="btn btn-outline-primary">
                        <i style="display: flex; justify-content: center; align-items: center; margin-top: 2px; color: #ffffff;" class="fa-solid fa-pen-to-square"></i>
                      </a>
                   </td>
       </tr>
       
       ';
   }
}

function editproduct(){
   global $connection;
  if(isset($_POST['btn_update_product'])){
   $id = $_POST['id'];
   $new_name_product = $_POST['product_name'];
    $new_image_product = upload_image_product('image-product');
    $new_price_product = $_POST['price_product'];
    $new_quantity_product = $_POST['quantity_name'];
    $new_brand_product = $_POST['brand_product'];
    $new_category_product = $_POST['product_category'];
    if(!empty($new_name_product) && !empty($new_image_product) && !empty($new_price_product) && !empty($new_quantity_product) && !empty($new_brand_product)){
       $sql_update_product = " UPDATE `db_product` 
                              SET 
                              `name`='$new_name_product',
                              `quantity`='$new_quantity_product',
                              `image`='$new_image_product',
                              `price`='$new_price_product',
                              `category`='$new_category_product',
                              `brand`='$new_brand_product'
                               WHERE `id` = '$id' 
                              ";
                              $result_update_product = $connection -> query($sql_update_product);
                              if($result_update_product){
                                   echo '
                                    <script>
                                $(document).ready(function(){
                                    swal({
                                        title: "Success",
                                        text: "Your Product Has Been Update Sucessfully",
                                        icon: "success",
                                        button: "Confirm",
                                    });
                                })
                            </script>';
                              }

    }
  }
}
editproduct();
function update_account(){
   global $connection;
   if(isset($_POST['btn_update_account'])){
$new_username = $_POST['new_name'];
$new_email    = $_POST['new_email']; 
$new_password = md5($_POST['new_password']);
$old_password = md5($_POST['old_password']);
$id = $_POST['id'];
$new_image_user = upload_image('new_image_for_user');
         if(!empty($new_email) && !empty($new_username) && !empty($new_password) && !empty($new_image_user)){
            if(empty($new_password) && empty($new_password) ){
     echo '
                                    <script>
                                $(document).ready(function(){
                                    swal({
                                        title: "Error",
                                        text: "Please Input new Password And Your Old Password",
                                        icon: "success",
                                        button: "Confirm",
                                    });
                                })
                            </script>';
                            return;
}
            $sql_update_account = "UPDATE `db_admin` 
              SET 
   `username`='$new_username',
   `email`='$new_email',
   `password`='$new_password',
   `image`='$new_image_user' 
WHERE `id` = '$id'";
        $result_update_account = $connection -> query($sql_update_account);
      if($result_update_account){
            echo '
                                    <script>
                                $(document).ready(function(){
                                    swal({
                                        title: "Success",
                                        text: "Your Account Has Been Change ",
                                        icon: "success",
                                        button: "Confirm",
                                    });
                                })
                            </script>';
        }
    }

else{
   $sql_update_account = "SELECT * FROM  `db_admin` WHERE `password` = '$old_password' ";
   $result_update_account = $connection -> query($sql_update_account);
   if($result_update_account){
       echo '
                                    <script>
                                $(document).ready(function(){
                                    swal({
                                        title: "Error",
                                        text: "You Input Wrong Password",
                                        icon: "error",
                                        button: "Confirm",
                                    });
                                })
                            </script>';
         }
     }    
          
  }
}
update_account();
function search_product(){
    global $connection;
    $search = $_GET['query'];
    $sql_search_product = "SELECT * FROM `db_product` WHERE `name` LIKE '%$search%'";
    $result_search_product = $connection -> query($sql_search_product);
    while($row = mysqli_fetch_assoc($result_search_product)){
    echo '<tr>
                                    
                                  <td scope="row"><b>'.$row['id'].'</b></td>
                                    <td style="font-size: 15px;">
                                    <b>'.$row['name'].'</b>
                                    </td>
                                    <td>
                            <img src="./img/Product-img/'.$row['image'].'" width="100px">
                                    </td>
                                    <td><b>'.$row['category'].'</b></td>
                                    <td><b>'.$row['brand'].'</b></td>
                                    <td><b>'.$row['price'].'</b></td>
                                    <!-- <td>16:00, 12 NOV 2018</td> -->
                                      <td>
                        <button type="button" style="width: 30px; height: 30px; border-radius: 10px;" class="btn btn-outline-danger mx-2 " name="btn_del">
                        <i style="display: flex; justify-content: center; align-items: center;" class="far fa-trash-alt tm-product-delete-icon"></i>
                      </button>
                       <a href="edit-product.php?id='.$row['id'].'" style="width: 30px; height: 30px; border-radius: 10px;"  class="btn btn-outline-primary">
                        <i style="display: flex; justify-content: center; align-items: center; margin-top: 2px; color: #ffffff;" class="fa-solid fa-pen-to-square"></i>
                      </a>
                   </td>
       </tr>
       
       '; 
    
    }
}
function upload_image($name){
   $thumbnail = date('YmdHis').'-'.$_FILES[$name]['name'];
   $path = 'img/image-user/'.$thumbnail;
   move_uploaded_file($_FILES[$name]['tmp_name'],$path);
   return $thumbnail;

}
function upload_image_product($data){
     $thumbnails = date('YmdHis').'-'.$_FILES[$data]['name'];
     $path = 'img/Product-img/'.$thumbnails;
     move_uploaded_file($_FILES[$data]['tmp_name'],$path);
     return $thumbnails;
}

?>