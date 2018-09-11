<?php 

 if(empty($_FILES['logo'])){
  exit('必须上传文件');
 }

 $file = $_FILES['logo'];

 if($file['error']!==UPLOAD_ERR_OK){
  exit('上传失败');
 }

//$ext = pathinfo($file['name'],PATH_EXTENSION);

 $target = '../../static/uploads/logo/img-' . uniqid() . '-' . $file['name'];

 if(!move_uploaded_file($file['tmp_name'], $target)){
    exit('上传失败');
 }

 echo substr($target,5);