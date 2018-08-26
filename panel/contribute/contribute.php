<?php

if(isset($_POST['authcode'])) {
  session_start();
  if($_POST['authcode'] == $_SESSION['authcode']) {
    require_once '../../php/connect-my-db.php';
    include '../../php/user.php';


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
      echo "投稿失败, 这个世界太♂乱".$conn->error;
      exit();
    }
    else {
      $sql = "SELECT * FROM userdata WHERE ukey='".generate_hash($author, $salt)."'";
      $res = $conn->query($sql);
      if($res && $res->num_rows > 0) {
        echo "投稿成功! 请耐心等待私站审核~";
        // sendmail("70851867@qq.com", "用户".$author."投稿了《".$title."》", "Trisolaris");
        exit();
      } else {
        echo "投稿失败, 系统怀疑你使用了假的令牌, 请注册！".$conn->error;
        exit();
      }
    }
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