<?php

header("content-Type: text/html; charset=utf-8");
require 'connect-my-db.php';

$great_title = "";

/*
  $conn, mysql数据库链接对象
  $tablename 数据表名
  $depth 起始深度
*/
function getnav($conn, $tablename, $depth = 0, $arl_pretend="") {
  global $great_title;
  $tree = array();
  $sql = "SELECT * FROM ".$tablename." WHERE root='".$depth."'";
  // echo $sql."<br>";
  $res = $conn->query($sql);
  if($res && $res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
      $arl = $row['arl']; // 文章唯一标识符id
      // 唯一标识符为空， 可能有两种情况
      //  1. 是一级标题(实际显示为2级, 因为还有一个更大的标题(#笑~))， 不需要arl
      //  2. 无效的链接(可能是无效的文章之类, 这里应不跳转， 但保留标签的变色效果)
      if(empty($arl)) { // 如果唯一标识符为空
        if($row['root'] == 0) {  // 如果是一级菜单
          $arl = "";
        }
        else {
          $arl = "javascript: void(0);";
        }
      }
      else {
        $arl = $arl_pretend."a/".$arl."/";
      }
      // 如果根就是自己
      // 那么标记为页面的大标题
      if($row['id'] == $row['root']) {
        $great_title = $row['navname'];
        $tree[] = $great_title;
        continue;
      }
      if($row["is_leaf"] != 1) {  // 如果当前不是叶节点
        // 递归添加子节点
        $child = getnav($conn, $tablename, $row['id'], $arl_pretend);
        $tree[] = array('val'=>$row['navname'], 'arl'=>$arl, 'child'=>$child);
      }
      else {
        $tree[] = array('val'=>$row['navname'], 'arl'=>$arl);
      }
    }
  }
  return $tree;
}


// 在$level >= 3时候添加a标签
// 暂时不用(或有需要优化的地方)
// a标签已经偷懒用css写掉了
// a[href='']...
// 5分钟后: 真香
function add_link_if($level, $is_front, $href="javascript:;", $is_blank=true) {
  if($level >= 3) {
    if($is_front) {
      if($is_blank) {
        return "<a href='".$href."' target='_blank'>";
      }
      return "<a href='".$href."'>";
    }
    else {
      return "</a>";
    }
  }
  return "";
}



function insertnav($arr, $level) {
  $navname = "";
  foreach($arr as $item) {
    // 如果含有child
    if(is_array($item)) {
      $val = $item['val'];
      $arl = $item['arl'];
      if(array_key_exists("child", $item)) {
        echo "<li><h".$level.">".add_link_if($level, true, $arl).$val.add_link_if($level, false)."</h".$level.">"."<ul>";
        insertnav($item['child'], $level + 1);
        echo "</ul></li>";
      }
      else {
        echo "<li><h".$level.">".add_link_if($level, true, $arl).$val.add_link_if($level, false)."</h".$level."></li>";
      }
    }
  }
}


function print_title() {
  global $great_title;
  echo $great_title;
}

?>