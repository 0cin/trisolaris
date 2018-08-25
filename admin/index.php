<?php

require_once '../php/connect-my-db.php';
include '../php/user.php';

$great_title = "";
$key = "";

if(isset($_POST['ukey'])) {
  $ukey = $_POST['ukey'];
  $key = $ukey;
  $sql = "SELECT * FROM userdata WHERE ukey='".generate_hash($ukey, $salt)."'";
  $res = $conn->query($sql);
  if($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if($row['permission'] >= 3) {
      echo "accept";
      exit();
    }
  } else {
    echo "deny";
    exit();
  }
}

?>
<?php
  require_once '../php/connect-my-db.php';

  /*
    $depth: 开始
    $subtable: 搜索到的子表
  */
  function read_nav($table, $depth, $subtable) {
    global $conn;
    global $great_title;
    $tree = array();
    // 先搜索深度为0的
    // 可以搜索到的有
    //  1. 巨大标题(1级标题)
    //  2. 大标题(2级标题)
    // 以上两种标题在私站中不会带有有效连接.
    $sql = "SELECT * FROM ".$table." WHERE root='".$depth."'";
    $res = $conn->query($sql);
    if($res && $res->num_rows > 0) {
      while($row = $res->fetch_assoc()) {
        // 取出所有属性
        $id = $row['id'];
        $navname = $row['navname'];
        $arl = $row['arl'];
        $root = $row['root'];
        $is_leaf = $row['is_leaf'];

        // 如果有唯一标识符
        if($arl != "") {
          $subtable[] = $navname;
        }
        // 如果根元素就是自己
        // 那么应该是一个巨大标题
        if($id == $root) {
          $tree[] = array('id'=>$id, 'navname'=>$navname, 'root'=>$root);
          $great_title = $navname;
          continue;
        }
        // 如果不是叶子节点
        // 说明底下还有元素
        if($is_leaf != 1) {
          $arr = read_nav($table, $id, $subtable);
          $tree[] = array('id'=>$id, 'navname'=>$navname, 'arl'=>$arl, 'root'=>$root, 'is_leaf'=>$is_leaf, 'child'=>$arr);
        }
        // 是叶子元素
        else {
          $tree[] = array('id'=>$id, 'navname'=>$navname, 'arl'=>$arl, 'root'=>$root, 'is_leaf'=>$is_leaf);
        }
      }
    }
    return $tree;
  }

  function output_nav($arra, $depth = 1) {
    foreach ($arra as $item) {
      $navname = $item['navname'];
      $id = $item['id'];
      $root = $item['root'];
      $str = "<button class='edit' id='$id' root='$root'>edit</button><button class='del' id='$id' root='$root'>del</button>";
      // 仅支持在第一级目录下在添加目录
      if($depth == 1) {
        $str = $str."<button class='add' id='$id' root='$root'>add</button>";
      }
      if(array_key_exists("child", $item)) {
        echo "<li><input value='".$navname."' disabled='disabled'>".$str."<ul>";
        output_nav($item['child'], $depth + 1);
        echo "</ul></li>";
      } else {
        echo "<li><input value='".$navname."' disabled='disabled'>".$str."</li>";
      }
    }
  }

  function title() {
    global $great_title;
    echo $great_title;
  }

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Trisolaris ADMIN</title>
  <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
  <script src="../js/jquery-3.3.1.js"></script>
  <script>
  var ukey = prompt("请出示管理员令牌");
  $.ajax({
    type: "post",
    url: "index.php",
    data: {
      ukey : ukey
    },
    dataType: "text",
    success: function (response) {
      if(response == "accept") {
      } else {
        location.reload();
      }
    }
  });
  $(document).ready(function () {
    // 点击添加键
    $(".add").click(function (e) {
      // e.preventDefault();
      var new_nav = prompt("输出要添加的分区名");
      if(new_nav != null) {
        $.ajax({
          type: "post",
          url: "../php/make_paration.php",
          data: {
            root: $(this).attr("id"),
            title: new_nav,
            key: ukey
          },
          dataType: "text",
          success: function (response) {
            alert(response);
          }
        });
      }
    });
    // 点击删除键
    $(".del").click(function (e) {
      // e.preventDefault();

    });
    // 点击编辑键
    $(".edit").click(function (e) {
      // e.preventDefault();

    });
  });

</script>
  <style>
    p {
      text-indent: 3em;
    }
  </style>
</head>
<body>
<?php
  $subtable = array();
  $tree = read_nav("indexnav", 0, $subtable);
?>
<h2>分区管理</h2>
<div id="partions-manager">
  <ul id="current-partions">
    <li><?php title(); ?>
      <ul>
        <?php
          output_nav($tree);
        ?>
      </ul>
    </li>

  </ul>
</div>
</body>
</html>