<?php

include '../components/connect.php';

session_start();

if(isset($_POST['name']) && isset($_POST['pass'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);
   $row = $select_admin->fetch(PDO::FETCH_ASSOC);

   if($select_admin->rowCount() > 0){
      $_SESSION['admin_id'] = $row['id'];
      header('location:dashboard.php');
   }else{
      $message[] = 'incorrect username or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Art Hand Kraft/Login</title>
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
        .btn-primary {
            color: #fff;
            background-color: #C6861A;
            border-color: #C6861A;
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
    </style>
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">


        <!-- Sign Up Start -->
        <div class="container-fluid">

            <div style="position: absolute;">
                <img src="../images/logo.png" width="250px" height="250px" style="border-radius: 50%; float: left; margin-left: 200px; margin-top: 200px;">
                <p style="margin-left: 80px; color: white; font-size: 60px; font-weight: bold; margin-top: 460px;">ART HAND KRAFT</p>
            </div>
            
               <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh; margin-left: 500px;">
                  <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                     <div class="bg-secondary rounded p-4 p-sm-5 my-4 mx-3" style="width:500px">
                           <div class="d-flex align-items-center justify-content-between mb-3">
                              <a href="index.html" class="">
                                 <!-- <h3 class="text-primary"><img src="../../images/logo.png" width="50px" height="50px" style="border-radius: 50%;"></h3> -->
                              </a>
                              <h3 style="margin:auto;">Login</h3>
                           </div>
                           <form action="" method="post">
                              <div class="form-floating mb-3">
                                 <input type="text" name="name" class="form-control" id="floatingText" placeholder="jhondoe" style="background-color: white">
                                 <label for="floatingText">Username</label>
                              </div>
                              <div class="form-floating mb-4">
                                 <input type="password" name="pass" class="form-control" id="floatingPassword" placeholder="Password" style="background-color: white">
                                 <label for="floatingPassword">Password</label>
                              </div>
                              <button type="submit" class="btn btn-primary py-3 w-100 mb-4" style="background-color: #C6861A ; border-color:#C6861A; font-size: large;">Login</button>
                           </form>
                     </div>
                  </div>
               </div>
            
        </div>
        <!-- Sign Up End -->
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