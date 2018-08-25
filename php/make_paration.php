<?php
/*
添加分区
与添加文章类似， 只不过操作更多一些
1. 检查用户权限， 如果用户权限不够， 则取消
2. 在dirpdata数据表中插入数据， 取得id.
3. 在indexnav数据表中插入数据， 设置arl为最新取得的id
4. 创建数据表nav.$id
5. 创建目录
6. 读取模板文件， 创建文件
*/

require_once '../php/connect-my-db.php';
include '../php/user.php';

$root = $_POST['root'];
$title = $_POST['title'];
$key = $_POST['key'];
$arl = "";
$author = "";

$sql = "SELECT * FROM userdata WHERE ukey='".generate_hash($key, $salt)."'";
$res = $conn->query($sql);
if($res->num_rows <= 0) {
  echo "不存在的令牌, 请先注册!".$sql;
  exit();
}
else {
  // 获得权限
  $row = $res->fetch_assoc();
  $permission = $row['permission'];
  $author = $row['username'];
  if($permission < 3) {
    echo "权限不足， 请重试!";
    exit();
  }
}

// 如果是插入二级菜单的话， 不用插入pdata
if($root > 0) {

  $sql = "INSERT dirpdata(dt, atype, author, title) VALUES('".date("Y-m-d h:i:s")."','1', '$author', '$title')";
  $res = $conn->query($sql);

  if($conn->error) {
      echo "插入数据库失败！".$conn->error;
      exit();
  }

  // 检索最新的id
  $sql = "SELECT * FROM dirpdata WHERE id=(SELECT MAX(id) FROM dirpdata)";
  $res = $conn->query($sql);
  if($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $arl = $row['id'];
  }
  else {
    echo "数据库查询失败!";
    exit();
  }
}


$sql = "INSERT indexnav(navname, arl, root, is_leaf) VALUES('$title', '$arl', '$root', '1')";
$conn->query($sql);
if($conn->error) {
  echo "插入数据库失败!".$conn->error;
  exit();
}
$sql = "UPDATE indexnav SET is_leaf='0' WHERE id='".$root."'";
$conn->query($sql);
if($conn->error) {
  echo "更新数据库失败".$conn->error;
  exit();
}

if($root > 0) {
  /* 创建数据表nav.$arl */
  $sql = "CREATE TABLE nav".$arl.
    "(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    navname VARCHAR(100) NOT NULL,
    arl TEXT NOT NULL,
    root INT UNSIGNED NOT NULL,
    is_leaf BOOLEAN NOT NULL,
    PRIMARY KEY(id)
    )";
  $conn->query($sql);
  if($conn->error) {
    echo "创建数据库失败!".$conn->error;
    exit();
  }
  // 插入默认标题
  $sql = "INSERT nav".$arl."(id, navname, arl, root, is_leaf) VALUES('0', '$title', '', '0', '1')";
  $conn->query($sql);
  if($conn->error) {
    echo "插入数据库失败!".$conn->error;
    exit();
  }


  // 创建目录
  if(!mkdir("../a/".strval($arl), 0777)) {
    echo "创建目录失败!";
    exit();
  }
  // 打开缓冲区
  ob_start();
  // 读取模板文件
  @readfile("../a/paration_template.html");
  $text = ob_get_flush();
  $myfile = fopen("../a/".strval($arl)."/index.php", "w");
  $text = str_replace("{title}", $title, $text);
  $text = str_replace("{arl}", $arl, $text);
  fwrite($myfile,$text);
  ob_clean();
}
echo "插入新导航成功!";


?>