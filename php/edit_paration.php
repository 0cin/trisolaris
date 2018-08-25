<?php

require_once 'connect-my-db.php';
include 'user.php';

$id = $_POST['id'];
$key = $_POST['key'];
$table = $_POST['table'];
$content = $_POST['content'];

// 基本操作
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

$sql = "UPDATE ".$table." SET navname='".$content."' WHERE id='".$id."'";
$conn->query($sql);

if($conn->error) {
  echo "操作失败, 这个世界太♂乱 ".$conn->error;
  exit();
}
echo "修改成功!";
exit();



?>