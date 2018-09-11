<?php  

require_once '../../config.php';

if($_SERVER['REQUEST_METHOD']!=='GET'){
  exit;
}
if(empty($_GET['username'])){
  exit;
}
$email = $_GET['username'];
  $con = mysqli_connect(MY_HOST,MY_USER,MY_PASS,MY_NAME);
  /*if(!con){
    exit;
  }*/
  $que = mysqli_query($con,"select * from users where email = '{$email}'");
  if(!$que){
    exit;
  }
  $avatar = mysqli_fetch_assoc($que);
  if(empty($avatar)){
    exit;
  }
  echo json_encode($avatar);  