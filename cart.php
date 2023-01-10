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
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['quantity'];
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products shopping-cart">

<h3 class="heading">shopping cart</h3>

<div class="box-container">

<?php
   $total_price = 0;
   $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $select_cart->execute([$user_id]);
   if($select_cart->rowCount() > 0){
      while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
?>
<form action="" method="post" class="box">
   <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">

   <a href="quick_view.php?pid=<?= $fetch_cart['product_id']; ?>" class="fas fa-eye"></a>
   <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
   <div class="name"><?= $fetch_cart['name']; ?></div>

   
   <div class="flex">
      <?php
      $product_cart_id = $fetch_cart['product_id'];
      $select_product = $conn->prepare("SELECT * FROM `products` WHERE product_id = $product_cart_id");
      $select_product->execute();
      if($select_product->rowCount() > 0){

         while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
         
         if ($fetch_product['is_sale'] == 1){ ?>

         <div class="price"><span><del style="text-decoration:line-through; color:silver">$<?= $fetch_product['price']; ?></del><ins style="color:#67022f;"> $<?=$fetch_product['price_discount'];?></ins> </span></div>

         <?php } else { ?>

         <div class="name" style="color:green; padding:20px 0px">$<?= $fetch_product['price']; ?></div> <?php } ?>

         <?php if ($fetch_product['category_id'] != '9'){?>

         <input type="number" name="quantity" class="qty" min="1" max="99" value="<?=$fetch_cart['quantity'];?>">
         <button type="submit" class="fas fa-edit" name="update_qty"></button>
         <?php } else { ?>
         <input type="hidden" name="quantity" value="1">
         <?php } } } ?> 
   </div>
   <div class="sub-total"> Sub Total : <span>$<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?></span> </div>
   <input type="submit" value="delete item" onclick="return confirm('delete this from cart?');" class="delete-btn" name="delete">
</form>
<?php
$total_price += $sub_total;
   }
}else{
   echo '<p class="empty">your cart is empty</p>';
}
?>
</div>

<div class="cart-total">
   <p>Total Price : <span>$<?= $total_price; ?></span></p>
   <a href="shop.php" class="option-btn">continue shopping</a>
   <a href="cart.php?delete_all" class="delete-btn <?= ($total_price > 1)?'':'disabled'; ?>" onclick="return confirm('delete all from cart?');">delete all item</a>
   <a href="checkout.php" class="btn <?= ($total_price > 1)?'':'disabled'; ?>">proceed to checkout</a>
</div>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>