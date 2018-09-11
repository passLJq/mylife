<?php 
  require_once '../config.php';

  //下发session 
  session_start();

  function login(){
    if(empty($_POST['email'])){
      $GLOBALS['message'] = '用户名不能为空';
      return;
    }
    if(empty($_POST['password'])){
      $GLOBALS['message'] = '密码不能为空';
      return;
    }
    $email = $_POST['email'];
    $GLOBALS['message_email'] = $email;
    $password = $_POST['password'];

    $con = mysqli_connect(MY_HOST,MY_USER,MY_PASS,MY_NAME);
    if(!$con){
      $GLOBALS['message'] = '连接数据库失败';
      return;
    }
    $query = mysqli_query($con,"select * from users where email= '{$email}' limit 1;");
    if(!$query){
      $GLOBALS['message'] = '登录失败，请重试。';
      return;
    }
    $user = mysqli_fetch_assoc($query);

    if(empty($user)){
      $GLOBALS['message'] = '用户名不存在！';
      echo '1';
      return;
    }

    if(md5($password) != $user['password']){
      $GLOBALS['message'] = '密码错误！';
      return;
    }
    $GLOBALS['message'] = '登陆成功';

    //存一个登录标识
    //$_SESSION['is_logged_in'] = true;
    $_SESSION['current_login_user'] = $user;  

    header('Location:index1.php');
    return;
  }
  if($_SERVER['REQUEST_METHOD']==='POST'){
    login();
  }

  //用户退出登录
  if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action'] === 'logout'){
    unset ($_SESSION['current_login_user']);
    // header('Location:/admin/login.php');
  }
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="../static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../static/assets/css/index.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete = "off">
      <img src="../static/uploads/avatar_2.jpg" class="avatar">
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
      <div class="alert alert-danger">
        <strong><?php echo $message; ?></strong>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" value="<?php echo isset($message_email) ? $message_email : '' ?>" class="form-control" placeholder="邮箱" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
  <script src="../static/assets/vendors/jquery/jquery.min.js"></script>
  <script>
  $(function (){
      var emailFormat = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+){1}$/;
     $("#email").on('blur',function (){
      if(!$(this).val() || !emailFormat.test($(this).val())) return;
       $.ajax({
          url:'api/avatar.php',
          type:'GET',
          data:{username:$("#email").val()},
          success:function(res){
                    if(!res) return;
                      $('.avatar').animate({
                        top:-90
                      },200).on('load',function (){
                        $(this).animate({top:-60},200);
                      }).attr('src',JSON.parse(res).avatar);
             }
          })
      })
  })
  </script>
</body>
</html>
