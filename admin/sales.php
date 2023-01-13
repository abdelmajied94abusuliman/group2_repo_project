<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   $category_name = $_POST['category'];

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image']['size'];
   $image_tmp_name_01 = $_FILES['image']['tmp_name'];
   $image_folder_01 = '../uploaded_img/'.$image;


   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = 'product name already exist!';
   }else{

      $insert_products = $conn->prepare("INSERT INTO `products`(name, details, price, image, category_id) VALUES(?,?,?,?,?)");
      $insert_products->execute([$name, $details, $price, $image, $category_name]);

      if($insert_products){
         if($image_size_01 > 2000000){
            $message[] = 'image size is too large!';
         }else{
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            $message[] = 'new product added!';
         }

      }

   }  

};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/'.$fetch_delete_image['image']);
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:products.php');
}


if(isset($_POST['add-sale-on-all'])){

$percent_discount = $_POST['add-sale-on-all'] ;

$select_all_products = $conn->prepare("SELECT * FROM `products` WHERE is_sale='0'");
$select_all_products->execute();
if($select_all_products->rowCount()>0){
    while($fetch_all_products = $select_all_products->fetch(PDO::FETCH_ASSOC)){
        $id = $fetch_all_products['product_id'];
        $new_price = $fetch_all_products['price'] * (1-((int)$percent_discount/100));
        $insert_new_price = $conn->prepare("UPDATE `products` SET price_discount='$new_price' , is_sale = '1'
                                            WHERE product_id = '$id' ");
        $insert_new_price->execute();
        header('location:http://localhost/php_project/admin/sales.php');
    }
}

}



if(isset($_POST['remove-all-sales'])){

    $select_all_products = $conn->prepare("SELECT * FROM `products` WHERE is_sale='1'");
    $select_all_products->execute();
    if($select_all_products->rowCount()>0){
        while($fetch_all_products = $select_all_products->fetch(PDO::FETCH_ASSOC)){
            $id = $fetch_all_products['product_id'];
            $insert_new_price = $conn->prepare("UPDATE `products` SET is_sale = '0'
                                                WHERE product_id = '$id' ");
            $insert_new_price->execute();
            header('location:http://localhost/php_project/admin/sales.php');
        }
    } 

}



if( isset($_POST['add-sale-on-category'])){

    $category_id = $_POST['category'];
    $percent_discount = $_POST['add-sale-on-category'] ;
    $select_all_products = $conn->prepare("SELECT * FROM `products` WHERE is_sale='0' AND category_id='$category_id'");
    $select_all_products->execute();
    if($select_all_products->rowCount()>0){
        while($fetch_all_products = $select_all_products->fetch(PDO::FETCH_ASSOC)){
            $id = $fetch_all_products['product_id'];
            $new_price = $fetch_all_products['price'] * (1-((int)$percent_discount/100));
            $insert_new_price = $conn->prepare("UPDATE `products` SET price_discount='$new_price' , is_sale = '1'
                                                WHERE product_id = '$id' ");
            $insert_new_price->execute();
            header('location:http://localhost/php_project/admin/sales.php');
        }
    }
}



if( isset($_POST['remove-category-sale'])){

    $category_id = $_POST['category'];
    $select_all_products = $conn->prepare("SELECT * FROM `products` WHERE is_sale='1' AND category_id='$category_id'");
    $select_all_products->execute();
    if($select_all_products->rowCount()>0){
        while($fetch_all_products = $select_all_products->fetch(PDO::FETCH_ASSOC)){
            $id = $fetch_all_products['product_id'];
            $insert_new_price = $conn->prepare("UPDATE `products` SET is_sale = '0'
                                                WHERE product_id = '$id' ");
            $insert_new_price->execute();
            header('location:http://localhost/php_project/admin/sales.php');
        }
    }
}








?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Art Hand Kraft</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    
    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../images/logo.png">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/dashboardstyle.css" rel="stylesheet">

    <style>
        <?php include("../css/dashboardstyle.css") ?>

        .fa-bars:before {
            content: "\f0c9";
        }
        .btn-primary {
            background-color: rgb(0, 0, 69);
            border-color: rgb(0, 0, 69);
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            transition: 0.5s;
            z-index: 999;        }
        input {
            background-color: #fff !important;
        }
        .bg-secondary {
            background-color: rgb(0, 0, 69) !important;
        }
        .sales-btn {
            color: white;
            background-color: rgb(0, 0, 109);
            border: none;
        }
        .sales-btn1 {
            color: white;
            background-color: rgb(220, 0, 0);
            border: none;
        }
    </style>
</head>

<body style="background-color: black;">
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark" style="height: 100%;">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <img src="../uploaded_img/logo1.png" style="border-radius: 50%;" width="100px" height="100px" alt="0">
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="ms-3">
                        <h4 class="mb-0">

                        <?php $select_accounts = $conn->prepare("SELECT * FROM `admins` WHERE id = '$admin_id'");
                                $select_accounts->execute();
                                $admin_name = $select_accounts->fetch();
                                echo strtoupper($admin_name['name']);
                        ?>
                        </h4>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="dashboard.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="products.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Products</a>
                    <a href="sold.php" class="nav-item nav-link"><i class="fa-sharp fa-solid fa-store-slash me-2"></i>Sold</a>
                    <a href="sales.php" class="nav-item nav-link active"><i class="fa-brands fa-adversal me-2"></i>Sales</a>
                    <a href="category.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Category</a>
                    <a href="orders.php" class="nav-item nav-link"><i class="fa-solid fa-truck me-2"></i>Orders</a>
                    <a href="admin.php" class="nav-item nav-link"><i class="fa-sharp fa-solid fa-user-tie me-2"></i>Admins</a>
                    <a href="users.php" class="nav-item nav-link"><i class="fa-solid fa-user me-2"></i>Users</a>
                    <a href="../components/admin_logout.php" class="nav-item nav-link"><i class="fa-sharp fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">

            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4" style="background-color: #fff !important; ">
                            <div class="ms-3">
                                <form action="" method="post">
                                    <button type="submit" name="add-all-product-sale" class="mb-2 sales-btn" style="width:90%">Add Sale On All Products</button>
                                    <input type="number" name="add-sale-on-all" class="mb-0" style="color:black !important;width:82%;; border:1px solid silver"> %
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4" style="background-color: #fff !important; ">
                            <div class="ms-3">
                                <form action="" method="post">
                                    <button type="submit" name="add-category-sale" class="mb-2 sales-btn" style="width:90%">Add Category Sale</button>
                                    <select style="display: inline-block; height:28px" name="category" placeholder="enter product category" required class="box" required maxlength="500" cols="60" rows="10">
                                            <?php
                                                    $prepare_category = $conn->prepare("SELECT * FROM `category`");
                                                    $prepare_category->execute();
                                                    if($prepare_category->rowCount() > 0){
                                                        while($fetch_category = $prepare_category->fetch(PDO::FETCH_ASSOC)){
                                            ?>
                                            <option class="dropdown-item" name="category">
                                                    <?php 
                                                    echo $fetch_category['category_id'] . "/" . $fetch_category['category_name']; 
                                                    ?>
                                            </option>
                                            <?php 
                                                    } } else { echo 'There is no category. Please create one first.';} 
                                            ?>    
                                    </select>  
                                    <input type="number" name="add-sale-on-category" class="mb-0" style="color:black !important;width:32%; border:1px solid silver"> %
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4" style="background-color: #fff !important; ">
                            <div class="ms-3">
                                <form action="" method="post">
                                    <button type="submit" name="remove-category-sale" class="mb-2 sales-btn1" style="width:90%">Remove Category Sale</button>
                                    <select style="width:90%;; height:28px" name="category" placeholder="enter product category" required class="box" required maxlength="500" cols="60" rows="10">
                                            <?php
                                                    $prepare_category = $conn->prepare("SELECT * FROM `category`");
                                                    $prepare_category->execute();
                                                    if($prepare_category->rowCount() > 0){
                                                        while($fetch_category = $prepare_category->fetch(PDO::FETCH_ASSOC)){
                                            ?>
                                            <option class="dropdown-item" name="category" value="<?=$fetch_category['category_id']?>">
                                                    <?php 
                                                    echo $fetch_category['category_id'] . "/" . $fetch_category['category_name']; 
                                                    ?>
                                            </option>
                                            <?php 
                                                    } } else { echo 'There is no category. Please create one first.';} 
                                            ?>    
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4" style="background-color: #fff !important; ">
                            <!-- <i class="fa fa-chart-pie fa-3x text-primary" style="color:rgb(0, 0, 69) !important"></i> -->
                            <div class="ms-3">
                                <form action="" method="post">
                                    <button type="submit" name="remove-all-sales" class="mb-2 sales-btn1" style="width:90%">Remove Sale From All Products In Market</button>
                                    <!-- <h6 class="mb-0" style="color:black !important">$2606</h6> -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4" style="background-color: #fff !important; ">
                            <h6 class="mb-4">Add Products To Sale</h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php
                                            $numbering = 1;
                                            $select_products = $conn->prepare("SELECT * FROM `products` WHERE is_sale = '0' AND sold != store");
                                            $select_products->execute();
                                            if($select_products->rowCount() > 0){
                                                while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
                                                    $i=0;
                                        ?>
                                        <tr>
                                            <td><?= $numbering++; ?> 

                                            <td><?= $fetch_products['name']; ?></td>

                                            <td><img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="" width="50px" height="50px"></td> <!-- image -->

                                            <td style="color:rgb(0, 220, 0);">$<?= $fetch_products['price']; ?></td>

                                            <td><a href="add_sale.php?sale=<?= $fetch_products['product_id']; ?>" style="color:blue" class="option-btn">Add</a></td>
                                            </tr>
                                       <?php } } else{
                                                echo '<p class="empty">No Products Without Sale!</p>';
                                            } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4" style="background-color: #fff !important; ">
                            <h6 class="mb-4">Remove Products From Sale</h6>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Discount</th>
                                        <th scope="col">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                            $numbering = 1;
                                            $select_products = $conn->prepare("SELECT * FROM `products` WHERE is_sale='1' && sold != store");
                                            $select_products->execute();
                                            if($select_products->rowCount() > 0){
                                                while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
                                                    $i=0;
                                        ?>
                                        <tr>
                                            <td><?= $numbering++; ?> 

                                            <td><?= $fetch_products['name']; ?></td>

                                            <td><img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="" width="50px" height="50px"></td> <!-- image -->

                                            <td><del style="text-decoration:line-through; color:silver">$<?= $fetch_products['price']; ?></del></td>
                                            <td><ins style="color:rgb(0, 220, 0);"> $<?=$fetch_products['price_discount'];?></ins></td>

                                            <td><a href="remove_sale.php?removeSale=<?= $fetch_products['product_id']; ?>" style="color:red" class="option-btn">Remove</a></td>
                                            </tr>
                                       <?php } } else{
                                                echo '<p class="empty">No Products On Sale!</p>';
                                            } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>