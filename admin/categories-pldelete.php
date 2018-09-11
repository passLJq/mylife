<?php 
require_once '../config.php';
require_once MY_DIR . '\function.php';
/**
 * 删除提交过来的id数据
 */
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id = $_POST['id'];
  //转换成数字就无法批量删除
  //$id = (int)$id; 
  $rows = my_put_data("delete from categories where id in ({$id});");
}
