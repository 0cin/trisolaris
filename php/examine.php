<?php

require_once 'connect-my-db.php';


$id = $_POST['id'];
$title = "";
$author_key = "";
$content = "";

$sql = "SELECT * FROM contribution WHERE id='$id'";
$res = $conn->query($sql);

if($res && $res->num_rows > 0) {
  $row = $res->fetch_assoc();
  $title = $row['title'];
  $author_key = $row['author'];
  $content = stripslashes($row['content']);
}
else {
  echo "读取数据库失败! 这个世界太♂乱";
  exit();
}

// 创建目录
$dir = "../tmp/".strval($id);
if(!is_dir($dir)) {
  if(!mkdir($dir)) {
    echo "创建目录失败! ";
    exit();
  }
}
// 打开缓冲区
ob_start();
// 读取模板文件
@readfile("../a/examine_template.html");
$text = ob_get_flush();
$myfile = fopen("../tmp/".strval($id)."/index.html", "w");
$text = str_replace("{title}", $title, $text);
$text = str_replace("{id}", $id, $text);
$text = str_replace("{author}", $author_key, $text);
date_default_timezone_set(PRC);
$text = str_replace("{content}", $content, $text);
$text = str_ireplace("Upload", "../../panel/contribute/Upload", $text);
// $text = preg_replace('/<img(.*?)src=\"(.*?)\"(.*?)>/is', '<div class="scale">\\0</div>', $text);

fwrite($myfile,$text);
ob_clean();
echo "success";

?>