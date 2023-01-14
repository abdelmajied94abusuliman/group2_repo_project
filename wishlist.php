<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};


if(isset($_POST['delete'])){
   $wishlist_id = $_POST['id'];
   $delete_wishlist_item = $conn->prepare("DELETE FROM `favorite` WHERE id = ?");
   $delete_wishlist_item->execute([$wishlist_id]);
}

if(isset($_GET['delete_all'])){
   $delete_wishlist_item = $conn->prepare("DELETE FROM `favorite` WHERE user_id = ?");
   $delete_wishlist_item->execute([$user_id]);
   header('location:wishlist.php');
}



if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addTOcart'])){
   $product_id = $_POST['product_id'];
   $product_name = $_POST['name'];
   $product_price = $_POST['price'];
   $product_image = $_POST['image'];
   $product_quantity = $_POST['quantity'];

   $check_product_id = $conn->prepare("SELECT product_id FROM `cart` WHERE user_id = '$user_id'");
   $check_product_id->execute();
   

   $flag = true;

   while($fetch_product = $check_product_id->fetch(PDO::FETCH_ASSOC)){
      if (in_array($product_id, $fetch_product)){
         $flag = false;
         break;
      }
   };
   if($flag==true){
      $send_to_cart = $conn->prepare("INSERT INTO `cart` (user_id , product_id , name , price , image , quantity)
                                    VALUES (? , ? , ? , ?, ? , ?)"); 
      $send_to_cart->execute([$user_id , $product_id , $product_name , $product_price, $product_image, $product_quantity]);
   }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>wishlist</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <!-- <link rel="stylesheet" href="css/style.css"> -->

   <style>
   <?php 
include("css/style.css");

?>
</style>

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h3 class="heading">your wishlist</h3>

   <div class="box-container">

   <?php

      $grand_total = 0;
      $select_wishlist = $conn->prepare("SELECT * FROM `favorite` WHERE user_id = ?");
      $select_wishlist->execute([$user_id]);
      if($select_wishlist->rowCount() > 0){
         while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){
            
            $product_cart_id = $fetch_wishlist['product_id'];
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE product_id = $product_cart_id");
            $select_products->execute();
            if($select_products->rowCount() > 0){ 
               while ($select_product = $select_products->fetch(PDO::FETCH_ASSOC)){
                  $grand_total += $select_product['price'];
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="product_id" value="<?= $fetch_wishlist['product_id']; ?>">
      <input type="hidden" name="id" value="<?= $fetch_wishlist['id']; ?>">
      <input type="hidden" name="name" value="<?= $select_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $select_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $select_product['image']; ?>">
      <a href="quick_view.php?pid=<?= $fetch_wishlist['product_id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $select_product['image']; ?>" alt="">
      <div class="name"><?= $select_product['name']; ?></div>
      <div class="flex">
               <?php if ($select_product['is_sale'] == 1){ ?>

                  <div class="price" style="padding:7px 0px"><span><del style="text-decoration:line-through; color:silver">$<?= $select_product['price']; ?></del><span style="color:#67022f;"> $<?=$select_product['price_discount'];?></span> </span></div>

               <?php } else { ?>

                  <div class="name" style="color:#67022f; padding:20px 0px">$<?= $select_product['price']; ?></div> <?php } ?>

               <?php if (($select_product['store']-$select_product['sold']) != '1'){?>

                  <input style="margin-left: 160px ;" type="number" name="quantity" class="qty" min="1" max="<?= ($select_product['store']-$select_product['sold']) ?>" value="1">
                  

               <?php } else { ?>
                  <input type="hidden" name="quantity" value="1">
               <?php } ?> 

            </div>
            <button type="submit" class="btn" name="addTOcart">Add To Cart</button>
      <input type="submit" value="Delete Item" onclick="return confirm('delete this from wishlist?');" class="delete-btn" name="delete">
   </form>
   <?php
      } } } 
   }else{
      echo '<p class="empty">Your Favorite Is Empty</p>';
   }
   ?>
   </div>

   <div class="wishlist-total">
      <p>Total Price : <span>$<?= $grand_total; ?></span></p>
      <a href="shop.php" class="option-btn">Continue Shopping</a>
      <a href="wishlist.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('delete all from wishlist?');">Delete All Item</a>
   </div>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>