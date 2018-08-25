<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>公告(#严肃)</title>
  <link rel="shortcut icon" href="../../images/favicon.ico" type="image/x-icon">
</head>

<body>
  <div id="head">
    <h1>
      <a href="javascript:;">
        <?php
        require '../../php/load-nav.php';
        $tree = getnav($conn, "nav30", 0, "../../");
        print_title();
        ?>
      </a>
    </h1>
  </div>

  <div id="body">
    <ul id="great-nav">
      <?php
        insertnav($tree, 2);
      ?>
    </ul>
  </div>

</body>

</html>

</html>