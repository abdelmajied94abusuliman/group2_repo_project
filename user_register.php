<?php


$pass='';
$cpass='';
$name='';
$email='';



include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

      // Validate name
    if(preg_match("/^([a-zA-Z' ]+)$/",$_POST['name'])){  
        $name= $_POST['name'];
        $name = htmlspecialchars($name, ENT_QUOTES);
       }else{
           $message[]= 'Invalid name .';
       }

   // Validate email
   if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $email = $_POST['email'];
      $email = htmlspecialchars($email, ENT_QUOTES);
   }
   else{
      $message[] = 'Invalid EMAIL !!';}


  // Validate pass

//   $checkPass=preg_match('^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$^',$_POST['pass']);
  if(preg_match('^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$^',$_POST['pass']))
  {
   $pass = sha1($_POST['pass']);
     $pass =htmlspecialchars($pass, ENT_QUOTES);
 
  }
  else 
  {
   $message[]='Invalid Pass';
  }
  
  $cpass = sha1($_POST['cpass']);
  $cpass = htmlspecialchars($cpass, ENT_QUOTES);


     if($name!='' && $email!='' && $cpass!='' && $pass!=''){

      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
      $select_user->execute([$email,]);
      $row = $select_user->fetch(PDO::FETCH_ASSOC);
   
      if($select_user->rowCount() > 0){
         $message[] = 'email already exists!';
      }else{
         if( $pass != $cpass){
            $message[] = 'confirm password not matched!';
         }
         else{
            $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
            $insert_user->execute([$name, $email, $cpass]);
            $message[] = 'registered successfully, login now please!';
         }
      }
     }


 

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>register now</h3>
      <input type="text" name="name" required placeholder="enter your username" maxlength="20"  class="box">
      <input type="email" name="email" required placeholder="enter your email" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="enter your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="confirm your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" class="btn" name="submit">
      <p>already have an account?</p>
      <a href="user_login.php" class="option-btn">login now</a>
   </form>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>