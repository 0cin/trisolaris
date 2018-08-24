<?php
  require '../../php/normal.php';
  include '../../php/user.php';


  $content = $_POST['content'];
  $title = $_POST['title'];
  $key = $_POST['key'];
  $navname = $_POST['navname'];
  $second_nav_name = $_POST["second_nav_name"];
  $author = "";

  // 先查找是否有存在的用户
  $sql = "SELECT * FROM userdata WHERE ukey='".generate_hash($key, $salt)."'";
  $res = $conn->query($sql);
  if($res->num_rows <= 0) {
    echo "不存在的令牌, 请先注册!".$sql;
    exit();
  }
  else {
    // 获得作者的用户名
    $row = $res->fetch_assoc();
    $author = $row['username'];
  }

  // 将目录替换
  $content = str_ireplace("Upload", "../2/Upload", $content);
  // 生成静态html文件
  // 在dirpdata数据表中插入一行
  // 1: 文章
  // 0: 功能页
  $sql = "INSERT dirpdata(dt, author, title, atype) VALUES('".date("Y-m-d h:i:s")."','".$author."','".$title."','1')";
  $res = $conn->query($sql);

  if($conn->error) {
    echo "插入数据库失败！".$conn->error;
    exit();
  }
  // 在dirpdata中检索id最大的行
  // 也就是最新插入的行
  $sql = "SELECT * FROM dirpdata WHERE id=(SELECT MAX(id) FROM dirpdata)";
  $res = $conn->query($sql);
  $id = "";
  // 获得最大的id
  if($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $id = $row['id'];
  }
  // 创建目录
  if(!mkdir("../".strval($id), 0777)) {
    echo "创建目录失败!";
    exit();
  }
  // 打开缓冲区
  ob_start();
  // 读取模板文件
  @readfile("../template.html");
  $text = ob_get_flush();
  $myfile = fopen("../".strval($id)."/index.html", "w");
  $text = str_replace("{title}", $title, $text);
  $text = str_replace("{author}", $author, $text);
  $text = str_replace("{time}", date("Y-m-d h:i:sa"), $text);
  $text = str_replace("{content}", $content, $text);
  // $text = preg_replace('/<img(.*?)src=\"(.*?)\"(.*?)>/is', '<div class="scale">\\0</div>', $text);

  fwrite($myfile,$text);
  ob_clean();

  $arl = get_arl($navname);
  $tablename = "nav".strval($arl);

  $sql = "SELECT * FROM ".$tablename." WHERE navname='".$second_nav_name."'";
  $res = $conn->query($sql);

  if($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $root_id = $row['id'];
    $sql = "UPDATE ".$tablename." SET is_leaf=0 WHERE navname='".$second_nav_name."'";
    $conn->query($sql);
    if($conn->error) {
      echo "更新数据库失败!";
      exit();
    }
    $sql = "INSERT ".$tablename."(navname, arl, root, is_leaf) VALUES('".$title."','".strval($id)."','".$root_id."','1')";
    $conn->query($sql);
    if($conn->error) {
      echo "更改数据库失败!".$sql;
      exit();
    }
  }
  echo "您的稿件提交成功， 静候审核喵~( • ̀ω•́ )✧"


  ?>