<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['update'])){

   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $price = $_POST['price'];
   $details = $_POST['details'];
   $category_data = $_POST['category'];
   $quantity = $_POST['store'] + $_POST['store_qty'];

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, details = ?, category_id = ?, store = ? WHERE product_id = ?");
   $update_product->execute([$name, $price, $details, $category_data, $quantity, $pid]);

   $message[] = 'product updated successfully!';

   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/'.$image;

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE product_id = ?");
         $update_image->execute([$image, $pid]);
         move_uploaded_file($image_tmp_name, $image_folder);
         unlink('../uploaded_img/'.$old_image);
         $message[] = 'image updated successfully!';
         header('location:http://localhost/php_project/admin/products.php');
      }
   }
   header('location:http://localhost/php_project/admin/products.php');
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

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../images/logo.png">

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
                    <a href="products.php" class="nav-item nav-link active"><i class="fa fa-th me-2"></i>Products</a>
                    <a href="sold.php" class="nav-item nav-link"><i class="fa-sharp fa-solid fa-store-slash me-2"></i>Sold</a>
                    <a href="sales.php" class="nav-item nav-link"><i class="fa-brands fa-adversal me-2"></i>Sales</a>
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



            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4" style="background-color: #fff !important;">
                            <h5 class="mb-4">Edit Your Product</h5>
                            <?php
                                $update_id = $_GET['update'];
                                $select_products = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
                                $select_products->execute([$update_id]);
                                if($select_products->rowCount() > 0){
                                    while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
                            ?>
                            <form action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="pid" value="<?= $fetch_products['product_id']; ?>">
                            <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
                            <input type="hidden" name="store_qty" value="<?= $fetch_products['store']; ?>">


                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Edit Product Name</label>
                                    <input type="text" name="name" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"  value="<?= $fetch_products['name']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Edit Product Price</label>
                                    <input type="text" name="price" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"  value="<?= $fetch_products['price']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Edit Product Details</label>
                                    <input type="text" name="details" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"  value="<?= $fetch_products['details']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Edit Product Image</label>
                                    <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Quantity In Store</label>
                                    <input type="number" name="store" class="form-control" id="exampleInputPassword1">
                                </div>
                                <div class="mb-3">
                                <select name="category" placeholder="enter product category" class="box" required maxlength="500" cols="60" rows="10">
                                        <?php
                                                $prepare_category = $conn->prepare("SELECT * FROM `category`");
                                                $prepare_category->execute();
                                                if($prepare_category->rowCount() > 0){
                                                    while($fetch_category = $prepare_category->fetch(PDO::FETCH_ASSOC)){
                                        ?>
                                        <option class="dropdown-item" value="<?= $fetch_category['category_id'] ?>">
                                                <?php 
                                                echo $fetch_category['category_id'] . "/" . $fetch_category['category_name']; 
                                                ?>
                                        </option>
                                        <?php 
                                                } } else { echo 'There is no category. Please create one first.';} 
                                        ?>    
                                </select>
                                </div>

                                <button style="background-color: green;" type="submit" name="update" class="btn btn-primary" value="Update">Update</button>
                                <button style="background-color: yellow !important;" class="btn btn-primary"> <a href="products.php" class="option-btn">Go Back</a> </button>
                            </form>
                            <?php
                                    }
                                }else{
                                    echo '<p class="empty">no category found!</p>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4" style="background-color: #fff !important;">
                              <?php
                                    $update_id = $_GET['update'];
                                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
                                    $select_products->execute([$update_id]);
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                 ?>
                            <img src="../uploaded_img/<?= $fetch_products['image'];?>" width="550px" height="560px">
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
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