<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>your orders</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="icon" type="image/x-icon" href="./images/logo.png">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">your order/s</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * 
                                       FROM `orders`
                                       INNER JOIN `users` ON orders.user_id = users.user_id;");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
            if ($fetch_orders['user_id'] == $user_id){
               $order_id = $fetch_orders['order_id'];
   ?>
   <div class="box">
      <p> Placed On : <span><?= $fetch_orders['location']; ?></span> </p>
      <p> Name : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Number : <span><?= $fetch_orders['mobile']; ?></span> </p>
      <p> Products Ordered : </p>
      <?php 
         $select_details = $conn->prepare("SELECT * FROM `order_details` WHERE order_id = '$order_id'");
         $select_details->execute();
         if($select_details->rowCount()>0){
            while($fetch_order_details = $select_details->fetch(PDO::FETCH_ASSOC)){
               $i=0;
               $product_id = $fetch_order_details['product_id'];
               $know_product = $conn->prepare("SELECT * 
                                             FROM `order_details` 
                                             INNER JOIN products ON  order_details.product_id = products.product_id
                                             WHERE order_details.product_id = '$product_id'");
               $know_product->execute();
               if($know_product->rowCount()>0){
                  while($fetch_to_know_product_name = $know_product->fetch(PDO::FETCH_ASSOC)){
                     if ($i == 0 ){
                        $i++;
                     ?> <p># Name : <?= $fetch_to_know_product_name['name']; ?> | QTY : <?= $fetch_order_details['quantity']?> | Price For 1 Item : <?=$fetch_order_details['price']?></p> 
               <?php } } } } } ?>
      <p> Total Products : <span><?= $fetch_orders['total_quantity']; ?></span> </p>
      <p> Total Price : <span>$<?= $fetch_orders['total_price']; ?></span> </p>
      <p> Order Time : <span>$<?= $fetch_orders['order_time']; ?></span> </p>
   </div>
   <?php
      } 
      } }
      else {
         echo '<p class="empty">no orders placed yet!</p>';
      }
   ?>

   </div>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>