<?php 
  require_once '../config.php';
  require_once MY_DIR . '\function.php';

  my_current_user();

  //分类
  $cate = my_fetch_all('select * from categories;');
  //获得分类信息
  $where = '1=1';
  $search = '';
  if (isset($_GET['cate']) && $_GET['cate']!=-1) {
    $where .= ' and posts.category_id=' . $_GET['cate'];
    $search .= '&cate=' . $_GET['cate'];
  }

  //状态
  $dict = array(
      'published' => '已发布',
      'drafted' => '草稿',
      'trashed' => '回收站'
      ); 
  //获得状态信息
  if(isset($_GET['status']) && $_GET['status'] !== 'allSta'){
    $where .= " and posts.status = '{$_GET['status']}'";
    $search .= '&status=' . $_GET['status'];
  }
  //分页参数
  $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

  //单页容量
  $size = 20;
  //最大页码
      $allpage = (int)my_fetch_one("select 
        count(1) as num
        from posts
        inner join categories on posts.category_id = categories.id
        inner join users on posts.user_id = users.id where {$where};")['num'];
      $allpage_count = (int)ceil($allpage / $size);

  //页数值过大或过小跳转
     $page = $page < 1 ? 1 : $page;

     $page = $page > $allpage_count ? $allpage_count : $page;
  
  //越过的数据量
  $cross = ($page-1) * $size;

  //获取文章数据
  $posts = my_fetch_all("select 
                          posts.id,
                          posts.title,
                          posts.created,
                          posts.status,
                          users.nickname as user_name,
                          categories.name as cate_name
                        from posts
                        inner join categories on posts.category_id = categories.id
                        inner join users on posts.user_id = users.id
                        where {$where}
                        order by posts.created desc
                        limit {$cross},{$size};");

  //分页页码

  //计算页码
  $cansee = 7;
  $reg = ($cansee - 1) / 2; 
  $begin = $page-$reg;
  $end = $begin+$cansee;
  //确保开始页最下为1
  if($begin < 1){
    $begin = 1;
    $end = $begin+$cansee;
  }
  
  //确保结束页码最大不大于数据库
  if($end > $allpage_count+1){
    $end = $allpage_count+1;
    $begin = $end - $cansee;
    if($begin < 1 ){
      $begin = 1;
    }
  }
  
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs" style="margin:20px 0">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm plDel" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
          <select name="cate" class="form-control input-sm">
            <option value="-1">所有分类</option>
            <?php foreach ($cate as $item): ?>
              <option value="<?php echo $item['id']; ?>" <?php echo isset($_GET['cate']) && $_GET['cate'] == $item['id'] ? 'selected' : ''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="allSta">所有状态</option>
            <?php foreach ($dict as $item => $value): ?>
              <option value="<?php echo $item; ?>" <?php echo isset($_GET['status']) && $_GET['status'] == $item ? 'selected' : ''; ?>><?php echo $value; ?></option>
            <?php endforeach ?>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php my_pagination($page,$allpage_count,'/admin/posts.php?page=%d'.$search) ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item):?>
          <tr data-id="<?php echo $item['id'] ?>">
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['cate_name']; ?></td>
            <td class="text-center"><?php echo parsing_date($item['created']); ?></td>
            <td class="text-center"><?php echo parsing_status($item['status']); ?></td>
            <td class="text-center">
              <a href="/admin/post-add.php?id=<?php echo $item['id']; ?>" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/category-delete.php?code=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs" >删除</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="../static/assets/vendors/jquery/jquery.js"></script>
  <script src="../static//assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
  $(function (){
    var tbody = $('tbody');
    var checkbox = $('tbody input:checkbox');
    var checkAll = $('thead input:checkbox');
    var pl = $('.plDel');
    var checkArr = [];
    checkbox.on('change',function (){
      var id = $(this).parent().parent().attr('data-id');
      if($(this).prop('checked')){
        checkArr.push(id);
      }else {
        checkArr.splice(checkArr.indexOf(id),1);
      }
      if(checkArr.length){
        pl.fadeIn();
      }else {
        pl.fadeOut();
      }
    })

    checkAll.on('change',function (){
      checkbox.prop('checked',$(this).prop('checked'));
      if($(this).prop('checked')){
        checkbox.each(function(i,ele){
          var id = $(ele).parent().parent().attr('data-id');
          if(checkArr.indexOf(id)<0){
            checkArr.push(id);
          }
        })
      }else {
        checkArr=[];
      }
      if(checkArr.length){
        pl.fadeIn();
      }else {
        pl.fadeOut();
      }
    })

    pl.on('click',function (){
      var str = checkArr.join(',');
      $(this).attr('href','/admin/category-delete.php?code='+str);
    })

  })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
