<?php 
  require_once '../config.php';
  require_once MY_DIR . '\function.php';

  $user = my_current_user();
  $cate = my_fetch_all('select * from categories');

  function post_add(){

    global $user;    
    if(empty($_POST['title'])) {
      $GLOBALS['message'] = '必须输入标题';
      return;
    }
    if(empty($_POST['content'])) {
      $GLOBALS['message'] = '必须输入内容';
      return;
    }
    //处理图片
    $feature = $_FILES['feature'] ? $_FILES['feature'] : '';
    if(empty($_FILES['feature']['error'])){
      $target = '../static/uploads/posts-img/' . uniqid() . '-' . $feature['name'];
      if(!move_uploaded_file($feature['tmp_name'],$target)){
        $GLOBALS['message'] = '图片上传失败';
        return;
      }
      $img_file = $target;
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    //设置时区
    date_default_timezone_set('PRC');
    $created = $_POST['created'] ? $_POST['created'] : date('Y-m-d H:i:s');
    $slug = $_POST['slug']!='' ? $_POST['slug'] : uniqid();
    $category = $_POST['category'];
    $status = $_POST['status'];
    $img_file = isset($target) ? substr($target,3) : '';
    $row = my_put_data("insert into posts values(null,'{$slug}','{$title}','{$img_file}','{$created}','{$content}',0,0,'{$status}',1,1);");
    if($row>0){
      $GLOBALS['success'] = '保存成功！';
    }
  }

    if(empty($_GET['id'])){
          if($_SERVER['REQUEST_METHOD'] =='POST'){
            echo 'bbbbbb';
          post_add();
          header("Location:{$_SERVER['PHP_SELF']}");
        }
      }else {
          $current_edit_post = my_fetch_one("select * from posts where id={$_GET['id']};");
          if($_SERVER['REQUEST_METHOD'] =='POST'){
          post_edit();
          header("Location:{$_SERVER['PHP_SELF']}");
        }
  }
  function post_edit(){
    global $current_edit_post;
    $id = $current_edit_post['id'];
    $title = empty($_POST['title']) ? $current_edit_post['title'] : $_POST['title'];
    $content = empty($_POST['content']) ? $current_edit_post['content'] : $_POST['content'];
    $slug = empty($_POST['slug']) ? $current_edit_post['slug'] : $_POST['slug'];
    date_default_timezone_set('PRC');
    $created = empty($_POST['created']) ? date('Y-m-d H:i:s') : $_POST['created'];
    $status = empty($_POST['status']) ? $current_edit_post['status'] : $_POST['status'];
    $category = empty($_POST['category']) ? $current_edit_post['category_id'] : $_POST['category'];

    $feature = $_FILES['feature'];
    if(empty($_FILES['feature']['error'])){
      $target = '../static/uploads/posts-img/' . uniqid() . '-' . $feature['name'];
      if(!move_uploaded_file($feature['tmp_name'],$target)){
        $GLOBALS['message'] = '图片上传失败';
        return;
      }
    }
    echo $target;
    $img_file = isset($target) ? substr($target,3) : $current_edit_post['feature'];
    $rows = my_put_data("update posts set title='{$title}',slug='{$slug}',
created='{$created}',status='{$status}',content='{$content}',category_id='{$category}',feature='{$img_file}' where id={$id};");
    if($rows>0){
       $GLOBALS['success'] = '保存成功！';
    }

  }

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="../static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../static/assets/css/index.css">
  <link rel="stylesheet" href="../static/assets/vendors/simplemde/simplemde.min.css">
  <script src="../static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <nav class="navbar">
      <button class="btn btn-default navbar-btn fa fa-bars"></button>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><i class="fa fa-user"></i>个人中心</a></li>
        <li><a href="login.php"><i class="fa fa-sign-out"></i>退出</a></li>
      </ul>
    </nav>
    <div class="container-fluid">
      <div class="page-title">
        <?php echo isset($current_edit_post) ? "<h1>修改{$current_edit_post['title']}</h1>" : '<h1>写文章</h1>'; ?>
      </div>
      <?php if(isset($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message; ?>
        </div>
      <?php endif ?>
      <?php if(isset($success)): ?>
        <div class="alert alert-success">
          <?php echo $success; ?>
        </div>
      <?php endif ?>
      <?php if(isset($current_edit_post)): ?>
          <form class="row" style="margin-top:20px" action="/admin/post-add.php?id=<?php echo $current_edit_post['id']; ?>" method="post" enctype="multipart/form-data">
          <div class="col-md-9">
            <div class="form-group">
              <label for="title">标题</label>
              <input id="title" class="form-control input-lg" name="title" type="text" value="<?php echo $current_edit_post['title']; ?>">
              </div>
              <div class="form-group">
              <label for="content">内容</label>
              <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" value="<?php echo $current_edit_post['content']; ?>"></textarea>
              <!-- <script id="content" type="text/plain">请输入文本</script> -->
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" value="<?php echo $current_edit_post['slug']; ?>" >
              <p class="help-block">http://mylife/post/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="feature">特色图像</label>
              <!-- show when image chose -->
              <img class="help-block thumbnail" style="display: none">
              <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
            </div>
            <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach($cate as $item): ?>
                <option value="<?php echo $item['id'] ?>" <?php echo $current_edit_post['category_id']==$item['id'] ? 'selected' : ''; ?>><?php echo $item['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
              <input type="hidden" name="action" value="submitted">
              <button class="btn btn-danger btn-lg" type="submit">保存</button>
            </div>
          </div>
        </form>
      <?php else: ?>
        <form class="row" style="margin-top:20px" action="/admin/post-add.php" method="post" enctype="multipart/form-data">
          <div class="col-md-9">
            <div class="form-group">
              <label for="title">标题</label>
              <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
              </div>
              <div class="form-group">
              <label for="content">内容</label>
              <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea>
              <!-- <script id="content" type="text/plain">请输入文本</script> -->
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">http://mylife/post/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="feature">特色图像</label>
              <!-- show when image chose -->
              <img class="help-block thumbnail" style="display: none">
              <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
            </div>
            <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach($cate as $item): ?>
                <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
              <input type="hidden" name="action" value="submitted">
              <button class="btn btn-danger btn-lg" type="submit">保存</button>
            </div>
          </div>
        </form>
      <?php endif ?>
    </div>
  </div>
  
  <?php $current_page = 'post-add' ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="../static/assets/vendors/jquery/jquery.js"></script>
  <script src="../static//assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="../static/assets/vendors/simplemde/simplemde.min.js"></script>
  <script>
    /*UE.getEditor('content',{
      initialFrameHeight:300,
      autoHeightEnabled:false
    });*/
    var simplemde = new SimpleMDE({
      element: $('#content')[0],
      autoDownloadFontAwesome: false,
      status:false
    });
    <?php if(isset($message) || isset($success)): ?>
      setTimeout(function(){
        $(".alert").fadeOut();
      },2000);
    <?php endif ?>
    <?php if(isset($current_edit_post)): ?>
      simplemde.value("<?php echo $current_edit_post['content']; ?>")
    <?php endif ?>
    
  </script>
  <script>NProgress.done()</script>
</body>
</html>
