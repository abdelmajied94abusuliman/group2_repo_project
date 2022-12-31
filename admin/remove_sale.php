<?php include '../components/connect.php';

$id = $_GET['removeSale'];

$xx = $conn->prepare("UPDATE products set is_sale='0'
                        WHERE product_id='$id'");
$xx->execute();
header('location:sales.php');