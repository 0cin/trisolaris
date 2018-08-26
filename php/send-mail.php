<?php

require_once 'mail.php';
$title = $_POST['title'];
$author = $_POST['author'];

sendmail("70851867@qq.com", "$author 投稿了一篇文章《".$title."》, 请审核", "Trisolaris");


?>