<?php 
require_once '../config.php';
require_once MY_DIR . '\function.php';

if(isset($_GET['code'])){
  my_put_data("delete from posts where id in ({$_GET['code']})");
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
/**
 * 删除提交过来的id数据
 */
if($_SERVER['REQUEST_METHOD']==='GET'){
  if(empty($_GET['id'])){
  exit('缺少必要参数');
}
$id = $_GET['id'];
//转换成数字就无法批量删除
//$id = (int)$id; 
$rows = my_put_data("delete from categories where id in ({$id});");
}


if($_SERVER['REQUEST_METHOD']==='POST'){
  $res = my_fetch_all('select * from categories');
  $json = JSON_encode($res);
  echo $json;
}

//header('Location:/admin/categories.php');