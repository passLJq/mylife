<?php 
  require_once '../config.php';
  require_once MY_DIR . '\function.php';


  function add_data(){
    if(empty($_POST['name'])){
      $GLOBALS['message'] = '请完整填写表单';
      return;
    }
    if(empty($_POST['slug'])){
      $GLOBALS['message'] = '请完整填写表单';
      return;
    }
      $name = $_POST['name'];
      $slug = $_POST['slug'];

    $result = my_put_data("insert into categories values(null,'{$slug}','{$name}');");
    echo $result;
    $GLOBALS['success'] = $result > 0 ? '添加成功' : null ;
    $GLOBALS['message'] = $result <= 0 ? '添加失败！' : null;
  }
  function edit_data(){
    global $current_edit_cate;
    $id = $current_edit_cate['id'];
    $name = empty($_POST['name']) ? $current_edit_cate['name'] :  $_POST['name'];
    $slug = empty($_POST['slug']) ? $current_edit_cate['slug'] :  $_POST['slug'];
    $rows = my_put_data("update categories set slug='{$slug}',name='{$name}' where id =" . $id);
    $GLOBALS['success'] = $rows > 0 ? '更新成功！' : null;
    $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : null;
    $current_edit_cate=null;
  }
  if (empty($_GET['id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      add_data();
    }
} else {
    $current_edit_cate = my_fetch_one('select * from categories where id = ' . $_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        edit_data();
    }
}
  $categories = my_fetch_all('select * from categories;');
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
  <link rel="stylesheet" href="../static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../static/assets/css/index.css">
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
        <h1>分类目录</h1>
      </div>
      <?php if(isset($message)): ?>
      <div class="alert alert-danger">
        <strong><?php echo $message; ?></strong>
      </div>
      <?php endif ?>
      <?php if(isset($success)): ?>
      <div class="alert alert-success">
        <strong><?php echo $success; ?></strong>
      </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?><?php echo isset($current_edit_cate['id']) ? '?id=' . $current_edit_cate['id'] : '' ?>" method="post">
            <?php if(isset($current_edit_cate)): ?>
              <h2>修改分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input type="text" id="name" class="form-control" name="name" value="<?php echo $current_edit_cate['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" value="<?php echo $current_edit_cate['slug']; ?>">
              <p class="help-block">http://mylife-dev/admin/categories/<strong><?php echo $current_edit_cate['slug']; ?></strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">修改</button>
            </div>
              <?php else : ?>
                <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input type="text" id="name" class="form-control" name="name" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">http://mylife-dev/admin/categories/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
            <?php endif ?>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- 勾选复选框时，出现 -->
            <a class="btn btn-danger btn-sm pld" href="javascript:;" style="display: none" id="pld">批量删除</a>
          </div>
          <table class="table table-bordered table-hover table-striped">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody id="tbody">
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php' ?>
  <script src="../static/assets/vendors/jquery/jquery.js"></script>
  <script src="../static//assets/vendors/bootstrap/js/bootstrap.js"></script>
  
  <script>
  $(function(){
    <?php if(isset($message) || isset($success)): ?>
      setTimeout(function(){
        $(".alert").fadeOut();
      },2000);
    <?php endif ?>
    //ajax异步请求数据以及删除数据
    tableLoad();
  function tableLoad(){
    $.ajax({
      url:'category-delete.php',
      type:'post',
      dataType:'json',
      success:function(res){
        var res = res;
        var str='';
        for(var i =0;i<res.length;i++){
          var tr = $('<tr></tr>');
          var td1 = $('<td class="text-center"></td>');
          var inp1 = $("<input type='checkbox' class='tbc' value='"+res[i].id+"'>");
          td1.append(inp1);
          var td2 = $('<td>'+res[i].name+'</td>');
          var td3 = $('<td>'+res[i].slug+'</td>');
          var td4 = $('<td class="text-center"><a href="/admin/categories.php?id='+res[i].id+'" class="btn btn-info btn-xs edit">编辑</a><a code="'+res[i].id+'" href="javascript:;" class="btn btn-danger btn-xs delete">删除</a></td>')
          tr.append(td1);
          tr.append(td2);
          tr.append(td3);
          tr.append(td4);
          $('#tbody').append(tr);
        }
          $('.delete').click(function(){
              var code = $(this).attr('code');
              $.ajax({
                url:'category-delete.php',
                type:'get',
                data:{id: code},
                success:function(res){
                    console.log(res);
                    $('#tbody').empty();
                    tableLoad();
                }
              });
          })
          fn();
          pl_del();
      }
    })
  }
  //批量删除==================================================================
  var checkboxId = [];
  function fn(){
    $(".tbc").click(function (){
      var id = $(this).val();
      if($(this).prop('checked')){
        checkboxId.push(id);
      }else{
        checkboxId.splice(checkboxId.indexOf(id),1);
      }
      var bool=true;
      for(var i=0;i<$(".tbc").length;i++){
          if($(".tbc").eq(i).prop('checked')){
          }else{
            bool = false;
          }
      }

      fade(); 
      $('thead input').prop('checked',bool);
    })
    
  }
  //一键全选
  $('thead input').click(function (){
    checkboxId=[];
    for(var i=0;i<$('.tbc').length;i++){
      $('.tbc').eq(i).prop('checked',$(this).prop('checked'));
    }
    if($(this).prop('checked')){
      for(var i=0;i<$('.tbc').length;i++){
        if(checkboxId.indexOf($('.tbc').eq(i).val())<0){
          checkboxId.push($('.tbc').eq(i).val());
        }
      }
    }else {
      checkboxId=[];
    }
    fade();
  })
  //点击批量删除按钮，异步请求删除
  function pl_del(){
    $('#pld').on('click',function (){
      var str = checkboxId.join(',');//把id转换为字符串
      console.log(str);
      console.log(str);
      $.ajax({
           url:'categories-pldelete.php',
           type:'post',
           data:{id: str},
           success:function(res){
                $('#tbody').empty();
                tableLoad();
           }
         });
    })
  }
    
  //出现删除按钮的函数;
  function fade(){
      checkboxId.length ? $('#pld').fadeIn() : $('#pld').fadeOut();
  }
//================================================================================
  })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
