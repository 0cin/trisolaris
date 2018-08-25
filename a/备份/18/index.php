<?php

require '../php/connect-my-db.php';
include '../php/user.php';

header("Content-Type: text/html;charset=utf-8");




function make_ukey($len) {
  $str = "";
  for($i = 0; $i < $len; $i++) {
    $str = $str.strval(rand(0, 9));
  }
  return $str;
}

function return_json($code, $msg, $ukey="") {
  return json_encode(array('code'=>$code, 'msg'=>$msg, 'ukey'=>$ukey));
}

if (isset($_POST['authcode'])) {
  // 开启session
  session_start();
  // 如果验证码输入正确
  if (strtolower($_POST['authcode']) == $_SESSION['authcode']) {
    $username = $_POST['username'];
    $username_len = strlen($username);
    $username_max_len = 20;
    $ukey_len = 6;


    if($username_len > $username_max_len) {
      echo return_json(0, '用户名不得大于20个字符');
      // echo "用户名不得大于20个字符!";
      exit();
    }

    $sql = "SELECT userdata WHERE username='".$username."'";
    $res = $conn->query($sql);

    if($res && $res->num_rows > 0) {
      echo return_json(0, '用户名已存在');
      // echo "用户名已存在";
      exit();
    }

    // 生成key与加密key
    $ukey = make_ukey($ukey_len);
    $encrypted_ukey = generate_hash($ukey, $salt);
    // 保证key在数据表中不存在
    // 虽然几率蛮小的
    $sql = "SELECT * FROM userdata WHERE ukey='".$encrypted_ukey."'";
    $res = $conn->query($sql);
    while($res && $res->num_rows > 0) {
      $ukey = make_ukey($ukey_len);
      $encrypted_ukey = generate_hash($ukey, $salt);
      $res = $conn->query($sql);
    }
    // 插入用户数据
    // 普通用户permisson为1
    $sql = "INSERT userdata(username, ukey, permission) VALUES('".$username."','".$encrypted_ukey."','1')";
    $conn->query($sql);

    if($conn->error) {
      echo return_json(0, '插入数据库错误'.$conn->error);
      // echo "插入数据库错误!";
      exit();
    }

    echo return_json(1, '', $ukey);
    // echo "来♂";
    exit();

  } else {
    echo return_json(0, '验证码输入错误!');
    // echo "验证码输入错误!";
    exit();
  }
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Trisolaris 注册</title>
  <link rel="shortcut icon" href="../../images/favicon.ico" type="image/x-icon">
  <script src="../js/jquery-3.3.1.js"></script>
  <script src="../js/reg.js"></script>
  <style>
    .error {
      color: red;
    }
  </style>
</head>
<body>
  <p>用户名: <input type="text" name="username" id="input-username" maxlength="20"> </p>
  <p>验证码: <input type="text" name="authcode" id="input-authcode" maxlength="4"> </p>
  <p>
  <img src="../php/captcha.php?=<?php echo rand();?>" width="100" height="30" id="captcha_img">
  <a href="javascript:void(0)" onclick="document.getElementById('captcha_img').src='../php/captcha.php?r=' + Math.random()">换一个</a>
  </p>
  <button id="regbtn">注册</button>
  <span class="error"></span>
</body>
</html>