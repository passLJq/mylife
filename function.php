<?php 
  require_once 'config.php';
 /**
  * 封装公用函数
  */
 session_start();
/**
 * 获取当前用户信息，没有获取到则跳转到login页
 */
 function my_current_user() {
    if(empty($_SESSION['current_login_user'])){
      //当前用户未登陆，没有登陆信息
      header('Location:/admin/login.php');
      exit();
    }
    return $_SESSION['current_login_user'];
 }

/**
 * 通过数据库查询多条数据
 *  return 索引数组套关联数组
 */
 function my_fetch_all ($sql) {
    $con = mysqli_connect(MY_HOST,MY_USER,MY_PASS,MY_NAME);
    if(!$con) {
      exit('连接失败');
    }
    $que = mysqli_query($con,$sql);
    if(!$que) {
      //exit('查询失败');
      return false;
    }
    $result = array();
    while($row = mysqli_fetch_assoc($que)){
      $result[] = $row; 
    }
    return $result;
 }
 /**
  * 通过数据库查询单条信息
  *  return 关联数组
  */
 function my_fetch_one ($sql){
  $res = my_fetch_all ($sql);
  return isset($res[0]) ? $res[0] : null;
 }
 /**
  * 执行增删改sql语句
  */
 function my_put_data($sql){
    $con = mysqli_connect(MY_HOST,MY_USER,MY_PASS,MY_NAME);
    if(!$con) {
      exit('连接失败');
    }
    $que = mysqli_query($con,$sql);
    if(!$que) {
      //exit('查询失败');
      return false;
    }
    $rows = mysqli_affected_rows($con);
    mysqli_close($con);
    return $rows;
 }

 /**
  * 处理status类型数据转换
  */
  function parsing_status($status){
    $dict = array(
      'published' => '已发布',
      'drafted' => '草稿',
      'trashed' => '回收站'
      ); 
    return isset($dict[$status]) ? $dict[$status] : '未知';
  }
/**
 * /转换时间戳
 * @param         $created [description]
 * @return [type]          [description]
 */
  function parsing_date($created) {
    date_default_timezone_set('PRC');
    $time = strtotime($created);
    return date('Y年m月d日 H:i:s', $time);
  }
/**
 * /转换分类
 * @param  [int] $cate_id [description]
 * @return [string] return          [description]
 */
  function parsing_cate($cate_id){
    return my_fetch_one("select name from categories where id = {$cate_id}")['name'];
  }

  function parsing_user($user_id){
    return my_fetch_one("select nickname from users where id = {$user_id}")['nickname'];
  }

  /**
 * /输出分页链接
 * @param  integer $page   当前页码
 * @param  integer $total  总页数
 * @param  string  $format 链接模板，%d 会被替换为具体页数
 *   <?php my_pagination(2, 10, '/posts.php?page=%d'); ?>
 */
  function my_pagination($page,$total,$format,$cansee = 5){
    $left = floor($cansee / 2);
    $begin = $page - $left;
    $begin = $begin < 1 ? 1 : $begin;
    $end = $begin + $cansee -1;
    $end = $end > $total ? $total : $end;
    $begin = $end - $cansee + 1;
    if($page>1){
      printf('<li><a href="%s">上一页</a></li>',sprintf($format,$page-1));
    }
    if($begin > 1){
      printf('<li><a href="%s">1</a></li>',sprintf($format,1));
      printf('<li class="disable"><span>...</span></li>');
    }
    for($i=$begin;$i<=$end;$i++){
      $active = $page==$i ? 'class="active"' : '';
      printf('<li %s><a href="%s">%d</a></li>',$active,sprintf($format,$i),$i);
    }
    if($end < $total){
      printf('<li class="disable"><span>...</span></li>');
      printf('<li><a href="%s">%d</a></li>',sprintf($format,$total),$total);
    }
    if($page<$total){
      printf('<li><a href="%s">下一页</a></li>',sprintf($format,$page+1));
    }
  }