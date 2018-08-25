<?php

require_once 'connect-my-db.php';
include 'user.php';

if(isset($_POST['code'])) {
  $code = $_POST['code'];
  $id = $_POST['id'];
  $key = $_POST['key'];
  $conent = "";
  $firstnav = "";
  $secondnav = "";
  $author = "";
  $title = "";
  // accept
  if($code == 1) {
    $sql = "SELECT * FROM contribution WHERE id='$id'";
    $res = $conn->query($sql);
    // 取得基本参数
    if($res && $res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $content = stripslashes($row['content']);
      $firstnav = stripslashes($row['firstnav']);
      $secondnav = stripslashes($row['secondnav']);
      $title = $row['title'];
    }
    $sql = "SELECT * FROM userdata WHERE ukey='".generate_hash($key, $salt)."'";
    // 取得用户名
    $res = $conn->query($sql);
    if($res && $res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $author = $row['username'];
    }

    $sql = "INSERT dirpdata(dt, author, title, atype) VALUES('".date("Y-m-d H:i:s")."','".$author."','".$title."','1')";
    $res = $conn->query($sql);

    if($conn->error) {
      echo "插入数据库失败！".$conn->error;
      exit();
    }
    // 在dirpdata中检索id最大的行
    // 也就是最新插入的行
    $sql = "SELECT * FROM dirpdata WHERE id=(SELECT MAX(id) FROM dirpdata)";
    $res = $conn->query($sql);
    $pdirid = "";
    // 获得最大的id
    if($res && $res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $pdirid = $row['id'];
    }
    // 创建目录
    if(!mkdir("../a/".strval($pdirid), 0777)) {
      echo "创建目录失败!";
      exit();
    }
    // 打开缓冲区
    ob_start();
    // 读取模板文件
    @readfile("../a/template.html");
    $text = ob_get_flush();
    $myfile = fopen("../a/".strval($pdirid)."/index.html", "w");
    $text = str_replace("{title}", $title, $text);
    $text = str_replace("{author}", $author, $text);
    date_default_timezone_set(PRC);
    $text = str_replace("{time}", date("Y-m-d H:i:s"), $text);
    $text = str_replace("{content}", $content, $text);
    $text = str_ireplace("Upload", "../../panel/contribute/Upload", $text);
    // $text = preg_replace('/<img(.*?)src=\"(.*?)\"(.*?)>/is', '<div class="scale">\\0</div>', $text);

    fwrite($myfile,$text);
    ob_clean();

    // 在firstnav/secondnav下插入当前创建的文章

    // 首先检索firstnav firstnav一定在indexnav数据表中

    $sql = "SELECT * FROM indexnav WHERE navname='".$firstnav."'";
    $res = $conn->query($sql);
    if($res && $res->num_rows > 0) {
      $row = $res->fetch_assoc();
      // 取得一级目录的arl
      $arl = $row['arl'];
    } else {
      echo "操作数据库失败， 这个世界太♂乱";
      exit();
    }
    // 然后再nav.$arl表中以secondnav为root插入当前创建的文章
    $tablename = "nav".$arl;
    $secondnav_id = "";
    $sql = "SELECT * FROM ".$tablename." WHERE navname='".$secondnav."'";
    $res = $conn->query($sql);

    if($res && $res->num_rows > 0) {
      $row = $res->fetch_assoc();
      // 获得二级目录的id
      $secondnav_id = $row['id'];
    }
    else {
      echo "操作数据库失败, 这个世界太♂乱";
      exit();
    }

    // 插入
    $sql = "INSERT $tablename(navname, arl, root, is_leaf) VALUES('$title', '$pdirid', '$secondnav_id', '1')";
    $conn->query($sql);

    if($conn->error) {
      echo "插入数据库失败!".$conn->error;
      exit();
    }

    $sql = "UPDATE ".$tablename." SET is_leaf='0' WHERE id='$secondnav_id'";
    $conn->query($sql);

    if($conn->error) {
      echo "更新数据库失败!".$conn->error;
      exit();
    }

    echo "审核通过".$id."号投稿! 请关闭当前页面";

  }
  // deny
  else if($code == 2) {

  }

  $sql = "DELETE FROM contribution WHERE id='$id'";
  $conn->query($sql);

  if($conn->error) {
    echo "操作数据出现异常， 这个世界太♂乱 ".$conn->error;
    exit();
  }

}

?>