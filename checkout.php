<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['order'])){

   $chose_name = $conn->prepare("SELECT * 
                                 FROM cart 
                                 INNER JOIN users WHERE cart.user_id = users.user_id");
   $chose_name->execute();
   $fetch_name_user = $chose_name->fetch(PDO::FETCH_ASSOC);

   $name = $fetch_name_user['name'];
   $number = $fetch_name_user['mobile'];
   $email = $fetch_name_user['email'];

   $address = 'flat no. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .','. $_POST['country'];
   $total_quantity = $_POST['quantity'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, location, total_quantity, total_price, order_time, mobile) VALUES(?,?,?,?,?,?)");
      date_default_timezone_set("Asia/Amman");
      $date_of_order = date("Y:m:d h:i:sa"); 
      $insert_order->execute([$user_id, $address, $total_quantity, $total_price, $date_of_order, $number]);

      $select_order_to_add = $conn->prepare("SELECT * FROM `orders`ORDER BY order_id DESC LIMIT 1;");
      $select_order_to_add->execute();
      if($select_order_to_add->rowCount()>0){
         while($fetch_order_to_details = $select_order_to_add->fetch(PDO::FETCH_ASSOC)){
            $order_id = $fetch_order_to_details['order_id']; } }
      
      $add_product_id_to_order_details = $conn->prepare("SELECT * FROM `cart` WHERE user_id='$user_id'");
      $add_product_id_to_order_details->execute();

      while ($fetch_to_take_product_id = $add_product_id_to_order_details->fetch(PDO::FETCH_ASSOC)){
         $product_id_in_cart = $fetch_to_take_product_id['product_id'];
         $product_quantity = $fetch_to_take_product_id['quantity'];
         $product_price = $fetch_to_take_product_id['price'];
         $insert_order_details = $conn->prepare("INSERT INTO `order_details`(order_id, product_id, quantity, price) VALUES(?,?,?,?)");
         $insert_order_details->execute([$order_id, $product_id_in_cart, $product_quantity, $product_price]);
      }


      while($fetch_cart_to_update = $check_cart->fetch(PDO::FETCH_ASSOC)){
         $id = $fetch_cart_to_update['product_id'];
         $update_products_after_sell = $conn->prepare("SELECT sold FROM `products` WHERE product_id = '$id' ");
         $update_products_after_sell->execute();
         $fetch_product_to_update_store = $update_products_after_sell->fetch(PDO::FETCH_ASSOC);
         $sold_item = (int)$fetch_product_to_update_store['sold'] + 1;
         $update_sold_product = $conn->prepare("UPDATE `products` SET sold ='$sold_item' WHERE product_id = '$id' ");
         $update_sold_product->execute();
      }


      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      

   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="icon" type="image/x-icon" href="./images/logo.png">

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

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>your orders</h3>

   <div class="display-orders">
      <?php
         $total_quantity=0;
         $total_price = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $total_price += ($fetch_cart['price'] * $fetch_cart['quantity']);
               $total_quantity += $fetch_cart['quantity'];
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].' x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">your cart is empty!</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $total_price; ?>" value="">
         <div class="grand-total">Total Price : <span>$<?= $total_price; ?></span></div>
      </div>

      <h3>place your orders</h3>

      <div class="flex">
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery" selected>cash on delivery</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Flat number :</span>
            <input type="text" name="flat" placeholder="e.g. flat number" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Street name :</span>
            <input type="text" name="street" placeholder="e.g. street name" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>City :</span>
            <input type="text" name="city" placeholder="e.g. mumbai" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Country :</span>
            <input type="text" name="country" placeholder="e.g. India" class="box" maxlength="50" required>
         </div>
      </div>
      <input type="hidden" name="quantity" value="<?= $total_quantity ?>">
      <input  type="submit" name="order" class="btn <?= ($total_price > 1)?'':'disabled'; ?>" value="place order" style="background-color:#67022f;">

   </form>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>