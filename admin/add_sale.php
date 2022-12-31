<?php include '../components/connect.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php'; ?>

    <section class="update-product">
        <h1 class="heading">Sale Form</h1> 
        <form action="" method="post" enctype="multipart/form-data">
            <span>Add New Form</span>
            <input type="number" name="new_price" required class="box" min="0" max="9999999999" placeholder="enter product price" onkeypress="if(this.value.length == 10) return false;">
            <div class="flex-btn">
                <input type="submit" name="update" class="btn" value="update">
                <a href="sales.php" class="option-btn">go back</a>
            </div>
        </form>
<script src="../js/admin_script.js"></script>
</body>
</html>

<?php
$id = $_GET['sale'];

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['new_price'])){
    $discount_price = $_POST['new_price'];
    $xx = $conn->prepare("UPDATE products set price_discount='$discount_price', is_sale='1'
                                    WHERE product_id='$id'");
    $xx->execute();
    header('location:sales.php');
}