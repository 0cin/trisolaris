<?php

if(isset($_POST['authcode'])) {
  session_start();
  if($_POST['authcode'] == $_SESSION['authcode']) {
    require_once '../../php/connect-my-db.php';

    /*
      标题
      作者
      内容
    */

    $title = $_POST['title'];
    $author = $_POST['author'];
    $content = $_POST['content'];
    // $content = mysql_escape_string($_POST['content']);
    $firstnav = $_POST['firstnav'];
    $secondnav = $_POST['secondnav'];

    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $firstnav = mysqli_real_escape_string($conn, $_POST['firstnav']);
    $secondnav = mysqli_real_escape_string($conn, $_POST['secondnav']);

    $sql = "INSERT contribution(title, author, content, firstnav, secondnav) VALUES('$title','$author','$content', '$firstnav','$secondnav')";
    $conn->query($sql);

    if($conn->error) {
      echo "上传失败, 这个世界太♂乱".$conn->error;
      exit();
    }
    echo "投稿成功! 请耐心等待私站审核~";
    exit();
  }
  else {
    echo "验证码错误!";
    exit();
  }
}
else {
  echo "弟♂大♂翻♂着♂洗";
  exit();
}



?>