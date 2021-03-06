<?php

require '../../php/connect-my-db.php';
include '../../php/user.php';

header("Content-Type: text/html;charset=utf-8");

$username = "";
$my_articles = array();

if(isset($_POST['ukey'])) {
  global $username;
  global $my_articles;
  $ukey = $_POST['ukey'];
  $encrypted_key = generate_hash($ukey, $salt);
  $sql = "SELECT * FROM userdata WHERE ukey='".$encrypted_key."'";
  $res = $conn->query($sql);
  if($conn->error) {
    // echo $conn->error;
    echo return_json(0);
    exit();
  }
  // 找到此令牌
  if($res && $res->num_rows > 0) {

    $row = $res->fetch_assoc();
    $username = $row['username'];
    // 搜索此人发布的文章
    $sql = "SELECT * FROM dirpdata WHERE author='".$username."'";
    $res = $conn->query($sql);
    if($res && $res->num_rows > 0) {
      while($row = $res->fetch_assoc()) {
        $id = $row['id'];
        $title = $row['title'];
        // 艹 我怎么百度都上不去???
        // 本来想弄一个按时间排序的
        // 等网络好点了再加吧
        $my_articles[] = array('id'=>strval($id), 'title'=>$title);
      }
    }
    // echo get_username();
    echo return_json(1, $username, $my_articles);
    exit();
  }
  echo return_json(0);
  exit();
}

if(isset($_POST['new_username'])) {
  $new_username = $_POST['new_username'];
  $origin_username = $_POST['origin_username'];

  if(empty($new_username)) {
    echo return_json(0, '用户名不能为空!');
    exit();
  }

  $sql = "SELECT username FROM userdata WHERE username='".$new_username."'";
  $res = $conn->query($sql);

  if($res && $res->num_rows > 0) {
    echo return_json(0, '已存在此用户名');
    exit();
  }
  $sql = "UPDATE userdata SET username='".$new_username."' WHERE username='".$origin_username."'";
  $conn->query($sql);

  if($conn->error) {
    echo return_json(0, '更改数据库失败!');
    exit();
  }
  echo return_json(1, "修♂改成功");
  exit();
}

function get_username() {
  global $username;
  echo $username;
}

function return_json($code, $msg="", $articles=array()) {
  return json_encode(array('code'=>$code, 'msg'=>$msg, 'articles'=>$articles));
}

?>
  <!DOCTYPE html>
  <html lang="zh-CN">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Trisolaris 管理用户</title>
    <link rel="shortcut icon" href="../../images/favicon.ico" type="image/x-icon">
    <script src="../../js/jquery-3.3.1.js"></script>
    <script>
      $(document).ready(function () {
        $("#keybtn").click(function (e) {
          // e.preventDefault();
          $("#origin-username").text("");
          $.ajax({
            type: "post",
            url: "index.php",
            data: {
              ukey: $("#input-ukey").val()
            },
            dataType: "json",
            success: function (response) {
              if (response.code == 0) {
                $("#origin-username").text("无效的令牌");
              } else {
                $("#origin-username").text(response.msg);
                if(response.articles != null) {
                  // 清空元素内容
                  $("#my-articles").empty();
                  response.articles.forEach(function(item){
                    $("#my-articles").append(
                      '<li><a href="../' + item.id + '/" target="_blank">' + item.title +
                      '</a><button class="editbtn" id="' + item.id + '">编辑</button><button class="delbtn" id="' + item.id + '">删除</button></li>'
                    );
                  });
                }
              }
            },
            error: function(response) {
              console.log("failed");
            }
          });
        });

        $("#login-btn").click(function (e) {
          // e.preventDefault();
          $("#prompt").text("");
          $.ajax({
            type: "post",
            url: "index.php",
            data: {
              origin_username: $("#origin-username").text(),
              new_username: $("#input-username").val()
            },
            dataType: "json",
            success: function (response) {
              $("#prompt").text(response.msg);

            }
          });
        });
      });

    </script>
  </head>

  <body>
    <h2>输入令牌</h2>
    <div id="head">
      <p>令牌:
        <input type="text" name="ukey" id="input-ukey" maxlength="6">
      </p>
      <button id="keybtn">确定</button>
    </div>
    <h2>用户名修改</h2>
    <div id="username-changer">
      <p>原用户名:
        <span id="origin-username"></span>
      </p>
      <p>新用户名:
        <input type="text" name="username" id="input-username" maxlength="20">
      </p>
      <button id="login-btn">修改</button>
      <br>
      <span id="prompt" style="color:red;"></span>
    </div>
    <h2>管理我的文章</h2>
    <div id="article-manager">
      <ul id="my-articles">
      </ul>
    </div>
  </body>

  </html>