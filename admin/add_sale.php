<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

$id = $_GET['sale'];

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['new_price'])){
    $discount_price = $_POST['new_price'];
    $xx = $conn->prepare("UPDATE products set price_discount='$discount_price', is_sale='1'
                                    WHERE product_id='$id'");
    $xx->execute();
    header('location:sales.php');
}


?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Art Hand Kraft/Add Sale</title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/dashboardstyle.css" rel="stylesheet">

    <style>
        <?php include("../css/dashboardstyle.css") ?>
        /* :root {
            --primary: #eb8f16;
            --secondary: #000000;
            --light: #6C7293;
            --dark: #000000;
        } */
        table {
            color :#fff !important;
        }
        .fa-bars:before {
            content: "\f0c9";
            color: white;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            background: #cf7b7b;
            transition: 0.5s;
            z-index: 999;
        }
        input {
            background-color: #fff !important;
        }
        label {
            color: #fff !important;
        }
        .bg-secondary {
            background-color: #67022f !important;
        }
        .nav-link{
            color: #fff !important;
        }
        .btn-primary {
            color: #fff;
            background-color: green !important;
            border-color: green;
        }
    </style>
</head>

<body style="background-color: black;">
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
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
                    <a href="sales.php" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>Sales</a>
                    <a href="category.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Category</a>
                    <a href="orders.php" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Orders</a>
                    <a href="users.php" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Users</a>
                    <a href="../components/admin_logout.php" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Logout</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <!-- <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto" style="min-height: 50px;">

                </div>
            </nav> -->
            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4">Add Sale To Your Product</h6>
                            <form action="" method="post">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Enter New Price</label>
                                    <input type="number" name="new_price" class="form-control" id="exampleInputEmail1"
                                        aria-describedby="emailHelp">
                                </div>
                                <input style="background-color: green;" type="submit" name="update" class="btn btn-primary" value="update">
                                <button style="background-color: yellow !important;" class="btn btn-primary"> <a href="sales.php" class="option-btn">go back</a> </button>
                            </form>
                        </div>
                    </div> <?php
                            $select_products = $conn->prepare("SELECT * FROM `products` WHERE product_id = '$id'");
                                $select_products->execute();
                                if($select_products->rowCount() > 0){
                                $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                        ?>
                    <div class="col-sm-12 col-xl-6">
                            <img src="../uploaded_img/<?= $fetch_products['image']; ?> ?>" width="400px" height="400px">
                    </div>
                    <?php } ?>
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

