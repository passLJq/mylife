<?php 
  //校验当前访问的用户有没有登录标识
  require_once '../config.php';
  require_once MY_DIR . '\function.php';
  //判断登陆
  my_current_user();
  //动态获取界面信息
  $posts_count = my_fetch_one('select count(1) as num from posts;');
  $posts_count_drafted = my_fetch_one("select count(1) as num from posts where status = 'drafted';");
  $comments_count = my_fetch_one('select count(1) as num from comments;')['num'];
  $comments_count_held = my_fetch_one("select count(1) as num from comments where status = 'held';")['num'];
  $categories_count = my_fetch_one('select count(1) as num from categories;')['num'];
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin</title>
  <link rel="stylesheet" href="../static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../static/assets/css/index.css">
  <script src="../static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>
  <div class="main">
    <?php include 'inc/navbar.php' ?>
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1 style="font-size:100px">My Life</h1>
        <p>Begins with a single step</p>
        <p><a href="post-add.php" class="btn btn-primary btn-lg" id="jl" role="button">记录生活</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">内容统计</h3>
            </div>
            <ul class="list-group">
            <li class="list-group-item"><strong><?php echo $posts_count['num']; ?></strong>篇文章<strong><?php echo $posts_count_drafted['num']; ?></strong>篇草稿</li>
              <li class="list-group-item"><strong><?php echo $categories_count; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments_count; ?></strong>条评论（<strong><?php echo $comments_count_held; ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4" id="main" style="height:300px">
          
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>
  <?php $current_page = 'index'; ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="../static/assets/vendors/jquery/jquery.js"></script>
  <script src="../static//assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="../static/assets/vendors/echart/echarts.simple.min.js"></script>
  <script>NProgress.done()</script>
  <script>
  window.onload=function(){
    var myChart = echarts.init(document.getElementById('main'));
    var option = {
    color: ['#d7493e'],
    tooltip : {
        trigger: 'axis',
        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis : [
        {
            type : 'category',
            data : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            axisTick: {
                alignWithLabel: true
            }
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'直接访问',
            type:'bar',
            barWidth: '60%',
            data:[10, 52, 200, 334, 390, 330, 220]
        }
    ]
};
    myChart.setOption(option);
  }
  </script>
</body>
</html>