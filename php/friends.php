<?php

require_once 'connect-my-db.php';

function read_friend(&$arr) {
  global $conn;
  $sql = "SELECT * FROM frlink";
  $res = $conn->query($sql);

  if($res && $res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
      $arr[] = array('prompt'=>$row['prompt'], 'link'=>$row['link'], 'id'=>$row['id']);
    }
  }


}

?>