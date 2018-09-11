<?php

require_once '../config.php';
  require_once MY_DIR . '\function.php';

  if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['id'])){
      $id = $_POST['id'];
      $page = $_POST['page'];
      $rows = my_put_data("delete from comments where id in ({$id});");
      echo $page;
    }
    if(isset($_POST['appr'])){
      $appr_id = $_POST['appr'];
      $page = $_POST['page'];
      $rows = my_put_data("update comments set status='approved' where id in ({$appr_id});");
      echo $page;
    }
    if(isset($_POST['rej'])){
      $rej_id = $_POST['rej'];
      $page = $_POST['page'];
      $rows = my_put_data("update comments set status='rejected' where id in ({$rej_id});");
      echo $page;
    }

  }
