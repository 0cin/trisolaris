<?php

require 'connect-my-db.php';

function get_arl($navname) {
  $arl = "";
  global $conn;
  // 本来可以在add_option_value的时候添加全局变量的
  // 这样就不用查数据库了
  // 无奈不知道为啥不行
  $sql = "SELECT * FROM indexnav WHERE navname='".$navname."'";
  $res = $conn->query($sql);
  if($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $arl = $row['arl'];
  }
  return $arl;
}

?>