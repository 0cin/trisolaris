<?php

  /*
    实现了基本删除功能
    挖个坑， 就是删除干净
    现在删的不干净， 好在删完之后不会互相影响， 不过数据表和目录会比较乱
    会有一些无用数据表和无用目录~
    嘛.. 先解决别的事!
  */
  require_once 'connect-my-db.php';
  include 'user.php';


  $id = $_POST["id"];
  $root = $_POST['root'];
  $key = $_POST['key'];
  $table = $_POST['table'];

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
  // 如果id == 0 直接清空整张表
  if($id == 1) {

    if($table != "indexdiv") {

    }
    $sql = "DELETE * FROM ".$table;
    // 如果不是从indexdiv中删除的
    // 那么应该是从indexdiv中伸出去的目录
    // 还要在indexdiv中删除之
    // 那么首先要获得此表在indexnav中的id
    // 表名是nav+数字的格式
    // 那么数字就是他在indexnav中的arl

    // 使用正则匹配数字
    preg_match_all("/[0-9]*/", $table, $match_res);
    if($table != "indexdiv") {
      $sql = "SELECT * FROM indexnav WHERE arl='".$match_res[1]."'";
      $res = $conn->query($sql);
      if($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        // 获取id
        $id = $row['id'];
        // 删除本尊以及以他为父的所有分区
        $sql = "DELETE FROM indexnav WHERE(id='$id' OR root='$id')";
        $conn->query($sql);
        if($conn->error) {
          echo "删♂除失败, 这个世界太♂乱 ".$conn->error;
          exit();
        }
      }
      else {
        echo "妖精の森里的蜜汁兔人(什么鬼几把错误提示)";
        exit();
      }
    }
  }
  else {
    $sql = "DELETE FROM ".$table." WHERE(id='$id' OR root='$id')";
  }
  $conn->query($sql);

  if($conn->error) {
    echo "删♂除失败,请检查你的操作！".$conn->error;
    exit();
  }

  echo "删除♂成功!";


?>