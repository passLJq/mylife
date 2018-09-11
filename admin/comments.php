<?php 
  require_once '../config.php';
  require_once MY_DIR . '\function.php';

  my_current_user();

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
      <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm pl-oper" ope="appr">批量批准</button>
          <button class="btn btn-warning btn-sm pl-oper" ope="rej">批量拒绝</button>
          <button class="btn btn-danger btn-sm pl-oper" ope="id">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right" id="pagination"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="130">操作</th>
          </tr>
        </thead>
        <tbody>
          
        
        </tbody>
      </table>
    </div>
  </div>
  
  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="../static/assets/vendors/jquery/jquery.js"></script>
  <script src="../static//assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="../static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script>
  var tbody = $('tbody');
  //ajax页面渲染
  tbodyLoad(1);

  function tbodyLoad(pageNum){
    //console.log(pageNum);
    $.ajax({
      url:'/admin/comments-get.php',
      type:'get',
      data : { page:pageNum },
      dataType:'json',
      success:function(data){
        var res = data['comm'];
        var total = data['total'];
        tbody.fadeOut(function(){
            tbody.empty();
            for(var i=0;i<res.length;i++){
            var tr = res[i].status=='held' ? $('<tr></tr>') : res[i].status=='approved' ? $('<tr class="info"></tr>') : $('<tr class="danger"></tr>')
            res[i].status = res[i].status=='held' ? '待审' : res[i].status=='approved' ? '批准' : '拒绝';
            tr.html('<td class="text-center"><input type="checkbox" value="'+res[i].id+'"></td><td>'+res[i].author+'</td><td>'+res[i].content+'</td><td>'+res[i].post_title+'</td><td>'+res[i].created+'</td><td>'+res[i].status+'</td>')
            var td = $('<td class="text-center"></td>');
            if(res[i].status=='批准'){
              td.html('<a href="javascript:;" class="btn btn-danger btn-xs del" id="delete">删除</a>');
            }else if(res[i].status=='待审'){
              td.html('<a href="javascript:;" class="btn btn-info btn-xs approved">批准</a><a href="javascript:;" class="btn btn-warning btn-xs rejected">拒绝</a><a href="javascript:;" class="btn btn-danger btn-xs del" id="delete">删除</a>');
            }else{
              td.html('<a href="javascript:;" class="btn btn-danger btn-xs del" id="delete">删除</a>');
            }
            tr.append(td);
            tbody.append(tr);
          }
          $(this).fadeIn();
          //del();
        })
        twbs(total,pageNum);
      }
    })
  }
  //给  删除  按钮添加点击事件，异步请求修改mysql数据
  $("tbody").on('click','.del',function (){
    var tr = $(this).parent().parent();
      var iD = tr.find('input').val();
      var p = $(".active a").html();
     $.post('/admin/comments-delete.php',{id:iD,page:p},function(res){
        var num = parseInt(res);
        $('#pagination').twbsPagination('destroy');
        tbodyLoad(num);
      })
  })
  //给  拒绝  按钮添加点击事件，异步请求修改mysql数据
  $("tbody").on('click','.rejected',function (){
    var tr = $(this).parent().parent();
    var iD = tr.find('input').val();
    var p = $(".active a").html();
    $.post('/admin/comments-delete.php',{rej:iD,page:p},function(res){
        var num = parseInt(res);
        $('#pagination').twbsPagination('destroy');
        tbodyLoad(num);
      })
  })
  //给  批准  按钮添加点击事件，异步请求修改mysql数据
  $("tbody").on('click','.approved',function (){
    var tr = $(this).parent().parent();
    var iD = tr.find('input').val();
    var p = $(".active a").html();
    $.post('/admin/comments-delete.php',{appr:iD,page:p},function(res){
        var num = parseInt(res);
        $('#pagination').twbsPagination('destroy');
        tbodyLoad(num);
      })
  })

  //点击checkbox事件
  var checkArr = [];  //批量操作id的数组
  tbody.on('change','input',function(){
    if($(this).prop('checked')){
      if(checkArr.indexOf($(this).val())<0){
        checkArr.push($(this).val());
      }
    }else {
      checkArr.splice(checkArr.indexOf($(this).val()),1);
    }
    if(checkArr.length){
      $('.btn-batch').show();
    }else {
      $('.btn-batch').hide();
    }
  })
  //全选checkbox
  $('thead input[type=checkbox]').on('change',function(){
    $('tbody input').prop("checked",$(this).prop('checked'));
    if($(this).prop('checked')){
      $('tbody input').each(function(index,ele){
        var id = $(this).val();
        if(checkArr.indexOf(id)<0){
          checkArr.push(id);
        }
      })
    }else {
      checkArr = [];
    }
    if(checkArr.length){
      $('.btn-batch').show();
    }else {
      $('.btn-batch').hide();
    }
  })
  //点击批量操作事件
  $('.pl-oper').on('click',function(){
    var oper = $(this).attr('ope');
    var iD = checkArr.join(',');
    var p = $(".active a").html();
    switch(oper){
      case 'appr': 
      $.post('/admin/comments-delete.php',{appr:iD,page:p},function(res){
        var num = parseInt(res);
        $('#pagination').twbsPagination('destroy');
        tbodyLoad(num);
      });
      break;
      case 'rej': 
      $.post('/admin/comments-delete.php',{rej:iD,page:p},function(res){
        var num = parseInt(res);
        $('#pagination').twbsPagination('destroy');
        tbodyLoad(num);
      });
      break;
      default : 
      $.post('/admin/comments-delete.php',{id:iD,page:p},function(res){
        var num = parseInt(res);
        $('#pagination').twbsPagination('destroy');
        tbodyLoad(num);
      });
      break;
    }
  })


  //twbs分页插件封装
  function twbs(total,pageNum){
    pageNum = pageNum > total ? total : pageNum;
    $('#pagination').twbsPagination({
            totalPages : total,
            visiablePages : 5,
            startPage : pageNum,
            onPageClick: function(e,page){
            tbodyLoad(page);
          }
        })
  }

  </script>

  <script>NProgress.done()</script>

</body>
</html>
