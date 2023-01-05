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

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      <?php include 'css/style.css'; ?>

   </style>

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">your order/s</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * 
                                       FROM `orders`
                                       INNER JOIN `users` ON Orders.user_id = users.user_id;");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
            if ($fetch_orders['user_id'] == $user_id){
   ?>
   <div class="box">
      <p> placed on : <span><?= $fetch_orders['location']; ?></span> </p>
      <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> number : <span><?= $fetch_orders['mobile']; ?></span> </p>
      <p> total products : <span><?= $fetch_orders['total_quantity']; ?></span> </p>
      <p> total price : <span>$<?= $fetch_orders['total_price']; ?></span> </p>
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