<?php  

  require_once '../config.php';
  require_once MY_DIR . '\function.php';

  $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
  $len = 15;
  $offset = ($page - 1) * $len;  
  $sql = sprintf("select
          comments.*,
          posts.title as post_title
          from comments
          inner join posts on comments.post_id = posts.id
          order by comments.created desc
          limit %d,%d;",$offset,$len);
  $count = my_fetch_one('select count(1) from comments');
  $total = ceil(($count['count(1)']) / $len);
  $comm = my_fetch_all($sql);
  $json = JSON_encode(array(
      'comm' => $comm,
      'total' => $total
    ));

  echo $json;