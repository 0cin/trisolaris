

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Trisolaris (我真的已经很努力啦!)</title>
  <link rel="stylesheet" href="css/general.css">
  <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">

</head>
<body>
  <div id="head">
  <h1><a href="javascript: void(0);" id="index-title">
  <?php
    // 预加载内容
    // 输出标题
    require_once 'php/load-nav.php';
    $tree = getnav($conn, "indexnav", 1);
    print_title();
  ?>
  </a></h1>
  </div>

  <div id="user-panel">
    <p>
      <span><a href="./panel/signup/" target="_blank" id="signup">注册</a></span>
      <span><a href="./panel/manage/" target="_blank" id="login">管理</a></span>
      <span><a href="./panel/contribute" target="_blank" id="contribute">投稿</a></span>
    </p>
  </div>

  <div id="features-panel">
  </div>
  <div id="index-body">
    <ul id="great-nav">
      <?php
        insertnav($tree, 2);
      ?>
    </ul>
  </div>

  <br><br>
  <footer>
  <h5>Copyright &copy; 2018 Sunshine+Ice All rights reserved.</h5>
  <?php
    include 'php/friends.php';
    $friend_arr = array();
    read_friend($friend_arr);
    // if(!empty($friend_arr)) {
    //   echo "<h5>友情链接</h5>";
    // }
    echo "<ul>";
    foreach($friend_arr as $item) {
      $id = $item['id'];
      $prompt = $item['prompt'];
      $link = $item['link'];
      echo "<li id='$id'><h5><a href='$link' target='_blank'>$prompt</a></h5></li>";

    }
    echo "</ul>"
  ?>
  </footer>
</body>
</html>