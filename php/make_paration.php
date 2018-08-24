<?php

require_once '../php/connect-my-db.php';

$title = $POST['title'];
$arl = $POST['arl'];
$key = $_POST['key'];

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
  if($permission < 3) {
    echo "权限不足， 请重试!";
    exit();
  }
}

$sql = "INSERT dirpdata(dt, atype) VALUES('".date("Y-m-d h:i:s")."','0')";
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
@readfile("../paration_template.html");
$text = ob_get_flush();
$myfile = fopen("../".strval($id)."/index.php", "w");
$text = str_replace("{title}", $title, $text);
$arl = str_replace("{arl}", $arl, $text);
fwrite($myfile,$text);
ob_clean();

/* 创建数据表nav.$arl */


?>