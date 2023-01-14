<?php

include '../components/connect.php';

session_start();

// التأكد من انه تم تسجيل دخول للادمن من خلال فحص ذاكرة السيشيين

$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
   header('location:admin_login.php');
};

// عند تعبئة فورم اضافة منتج جديد بالاسفل , تأكد من تعبئة البيانات التالي ثم القيام بتشفيرها


if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $name = htmlspecialchars($name, ENT_QUOTES);
   $price = $_POST['price'];
   $price = htmlspecialchars($price, ENT_QUOTES);
   $details = $_POST['details'];
   $details = htmlspecialchars($details, ENT_QUOTES);
   $category_id = $_POST['category'];
   $quantity = $_POST['store'];

// تحميل صورة و تشفيرها

// قراءة اسم الصورة
   $image = $_FILES['image']['name'];
// قراءة حجم الصورة
   $image_size = $_FILES['image']['size'];
// تحديد المسار الموجودة فيه الصورة
   $image_tmp_name = $_FILES['image']['tmp_name'];
// تحديد المسار الجديد للصورة و تذكر انه يجب انشاء مجلد جديد مشابه للاسم المختار في المسار الجديد
   $image_folder = '../uploaded_img/'.$image;


// قراءة جميع المنتجات الموجودة في الداتابيس لتأكد من ان اسم المنتج غير متكرر , جدول المنتجات-عمود الاسم

// علامة الاستفهام تعني انتظار عنصر في فانكشين ال الاكسكيوت , اذا بدك حط المتغير مباشرة ولكن الافضل هو هاي

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

// في حالة ايجاد الاسم اطبع انه المنتج موجود

   if($select_products->rowCount() > 0){
      $message[] = 'product name already exist!';

// غير ذلك , قم برفع المنتج الجديد الى قاعدة البيانات

   }else{

// القيام برفع كافة تفاصيل المنتج التي تم ادخالها و يجب التاكد من ان عدد الاعمدة مساوي لعدد البيانات المراد رفعها

    $insert_products = $conn->prepare("INSERT INTO `products`(name, details, price, image, category_id, store) VALUES(?,?,?,?,?,?)");
    $insert_products->execute([$name, $details, $price, $image, $category_id , $quantity ]);
      
// شرط للتأكد من ان حجم الصورة اقل من 2 ميجا


      if($insert_products){
         if($image_size > 2000000){
            $message[] = 'image size is too large!';
         }else{

// اذا كان حجم الصورة مسموح , انقل الصورة من المسار القديم الى المسار الجديد
            move_uploaded_file($image_tmp_name, $image_folder);
// متغير بتم عرضه دايما فوق مثل الاشعارات
            $message[] = 'new product added!';
         }

      }

   }  

};





// اذا بدك تمسح منتج

// اول اشي اتاكد من انه انكبس على كبسة الديليت 
if(isset($_GET['delete'])){

// اقرء الاي ديه الي مبعوث مع الرابط 
   $delete_id = $_GET['delete'];

// هون بدي امسح الصورة تبعت المنتج الي كبسة على الديليت تبعته
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/'.$fetch_delete_image['image']);

// هون بدي امسح المنتج كامل و بعدين اقله انقلني على صفحة المنتجات عشان ما اضطر اعمل ريفريش للصفحة لما احذف منتج
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE product_id = ?");
   $delete_product->execute([$delete_id]);
   header('location:products.php');
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
                        <div class="bg-secondary rounded h-100 p-4" style="background-color: #fff !important; ">
                            <h5 class="mb-4">Add New Product</h5>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Product Name</label>
                                    <input type="text" name="name" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Product Price</label>
                                    <input type="text" class="form-control" required id="exampleInputEmail1" aria-describedby="emailHelp" name="price">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Image</label>
                                    <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" required class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Product Details</label>
                                    <input type="text" name="details" class="form-control" required id="exampleInputPassword1">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Quantity In Store</label>
                                    <input type="number" name="store" class="form-control" required id="exampleInputPassword1">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Product Category</label>
                                    <select name="category" placeholder="enter product category" required class="box" required maxlength="500" cols="60" rows="10">
                                        <?php
                                        // اول اشي استدعي كل الاعمدة الي بجدول الكاتيجوري
                                                $prepare_category = $conn->prepare("SELECT * FROM `category`");
                                                $prepare_category->execute();
                                        // هون بسأله اذا اصلا فيه بيانات بجدول الكاتيجوري اول , الفانكشن (روو كاونت ) بحسب عدد الصفوف بالجدول فلو كان صفر يعني ما فيه داتا بهادا الجدول

                                                if($prepare_category->rowCount() > 0){
                                                    // اذا الجدول فيه داتا فبقله اقرألي هاي البيانات و اعطيها اسم الي هو فيتش_كاتيجوري
                                                    while($fetch_category = $prepare_category->fetch(PDO::FETCH_ASSOC)){
                                        ?>
                                        <option class="dropdown-item" name="category">
                                                <?php 
                                                // جوا تاج الاوبشن بقله اطبعلي الاي دييه لكل كاتيجوري بالاضافة لاسمها و بسكر التاج بعيدها
                                                echo $fetch_category['category_id'] . "/" . $fetch_category['category_name']; 
                                                ?>
                                        </option>
                                        <!-- هون بتكون جملة اللوب الاولى تبعت الوايل خلصت , فبرجع بلف كمان مرة و بطلع الكاتيجوري الثانية و هيك -->
                                        <?php 
                                        // هاي جملة الايلس تبعت في حال كان عدد الصفوف بالجدول يساوي صفر , طبعا اول قوس كيرلي هو تسكيرة قوس الوايل لانه لازم يكون بعد تاج الاوبشن حتى ما يصير فيه مشاكل بعرض الداتا
                                                } } else { echo 'There is no category. Please create one first.';} 
                                        ?>    
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" value="Add Product" name="add_product">Add Product</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4" style="background-color: #fff !important; ">
                            <img src="https://cdn.shopify.com/s/files/1/0277/3614/5999/articles/soorten-kunst.png?v=1623749344" width="550px" height="560px">
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Sale & Revenue End -->


            <!-- Admin Table -->

            
            <div class="container-fluid pt-4 px-4" style="margin-bottom: 30px;">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-secondary rounded h-100 p-4" style="background-color: #fff !important; ">
                            <h5 class="mb-4">Products Table</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Products Name</th>
                                            <th scope="col">Products Image</th>
                                            <th scope="col">Product Price</th>
                                            <th scope="col">Product Discount</th>
                                            <th scope="col">Product Category</th>
                                            <th scope="col">Product Details</th>
                                            <th scope="col">Remaining</th>
                                            <th scope="col">Product Update</th>
                                            <th scope="col">Product Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                        $numbering = 1;
                                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE store!=sold");
                                        $select_products->execute();
                                        if($select_products->rowCount() > 0){
                                            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
                                                $i=0;
                                    ?>
                                        <tr>
                                            <td><?= $numbering++; ?> 

                                            <td><?= $fetch_products['name']; ?></td>

                                            <td><img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="" width="50px" height="50px"></td> <!-- image -->

                                            <?php if ($fetch_products['is_sale'] == 1){ ?>

                                                <td><del style="text-decoration:line-through; color:silver">$<?= $fetch_products['price']; ?></del></td>
                                                <td><ins style="color:rgb(0, 220, 0);"> $<?=$fetch_products['price_discount'];?></ins></td>

                                                <?php } else { ?>
                                                <td style="color:rgb(0, 220, 0);">$<?= $fetch_products['price']; ?></td>
                                                <td>Not On Sale</td>
                                                <?php } 
                                            ?>

                                            <?php $product_category = $conn->prepare("SELECT * 
                                                                                    FROM `products`
                                                                                    INNER JOIN `category` ON products.category_id = category.category_id");
                                                    $product_category->execute();
                                                    if($product_category->rowCount() > 0){
                                                        while($fetch_product_category = $product_category->fetch(PDO::FETCH_ASSOC)){ 
                                                            if($i==0 && $fetch_products['category_id'] == $fetch_product_category['category_id'] ){
                                                            $i++;
                                            ?>

                                            <td>Category : <?= $fetch_product_category['category_name']; ?></td>

                                            <?php } } } ?>
         
                                            <td><?= $fetch_products['details']; ?></td>
                                            <td style="text-align: center;"><?= $fetch_products['store']-$fetch_products['sold']; ?></td>

                                            <td><a href="update_product.php?update=<?= $fetch_products['product_id']; ?>" style="color:blue" class="option-btn">Update</a></td>

                                            <td><a href="products.php?delete=<?= $fetch_products['product_id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">Delete</a></td>
                                        </tr>
                                       <?php } } else{
                                                echo '<p class="empty">no accounts available!</p>';
                                            } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sales Chart End -->


            <!-- Recent Sales Start -->
            
            <!-- Recent Sales End -->


            <!-- Widgets Start -->
            
            <!-- Widgets End -->


            <!-- Footer Start -->
            <!-- Footer End -->
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