<?php 

$current_page = empty($current_page)? '' : $current_page; 

require_once MY_DIR . '\function.php';
$current_user = my_current_user();

?>
<div class="aside">
    <div class="profile">
      <img src="<?php echo isset($current_user)?$current_user['avatar']:'../../static/uploads/avatar_2.jpg' ?>" class="avatar">
      <h3 class="name"><?php echo $current_user['nickname'] ?></h3>
    </div>
    <ul class="nav">
      <li <?php echo $current_page === 'index'? 'class="active"' : '';  ?>><a href="index1.php"><i class="fa fa-dashboard"></i>仪盘表</a></li>
      <?php $menu_post=array('posts','post-add','categories'); ?>
      <li <?php echo in_array($current_page,$menu_post) ? 'class="active"' : '';  ?>>
        <a href="#menu-posts" class="collapsed" data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" <?php echo in_array($current_page,$menu_post) ? 'class="collapse in"' : 'class="collapse"';  ?> >
          <li <?php echo in_array($current_page,$menu_post) ? 'class="active"' : '';  ?>><a href="posts.php">所有文章</a></li>
          <li <?php echo in_array($current_page,$menu_post) ? 'class="active"' : '';  ?>><a href="post-add.php">写文章</a></li>
          <li <?php echo in_array($current_page,$menu_post) ? 'class="active"' : '';  ?>><a href="categories.php">分类目录</a></li>
        </ul>
      </li>
       <li <?php echo $current_page === 'comments'? 'class="active"' : '';  ?>>
        <a href="comments.php"><i class="fa fa-comments"></i>评论</a>
      </li>
      <li <?php echo $current_page === 'users'? 'class="active"' : '';  ?>>
        <a href="users.php"><i class="fa fa-users"></i>用户</a>
      </li>
      <?php $menu_setting=array('nav-menus','slides','settings'); ?>
      <li <?php echo in_array($current_page,$menu_setting) ? 'class="active"' : '';  ?>>
        <a href="#menu-setting" class="collapsed" data-toggle="collapse">
          <i class="fa fa-cog fa-spin"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-setting" <?php echo in_array($current_page,$menu_setting) ? 'class="collapse in"' : 'class="collapse"';  ?>>
          <li <?php echo $current_page === 'nav-menus'? 'class="active"' : '';  ?>><a href="nav-menus.php">导航菜单</a></li>
          <li <?php echo $current_page === 'slides'? 'class="active"' : '';  ?>><a href="slides.php">图片轮播</a></li>
          <li <?php echo $current_page === 'settings'? 'class="active"' : '';  ?>><a href="settings.php">网站设置</a></li>
        </ul>
      </li>
    </ul>
  </div>
