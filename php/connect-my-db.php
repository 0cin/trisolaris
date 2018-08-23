<?php

header("content-Type: text/html; charset=utf-8");

$dbservername = "127.0.0.1";
$dbusername = "root";
$dbpassword = "";
$dbname = "trisolaris";
$error_code = 0;
$success_code = 1;

$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

// 链接数据库失败
if($conn->connect_error) {
  echo $conn->connect_error;
  exit();
}

$conn->query("set names utf8");


// 返回json数据
// code: 返回码
// msg: 返回消息
function write_reponse_json($code, $msg) {
  return json_encode(array('code'=>$code, 'msg'=>$msg));
}


?>