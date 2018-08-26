<?php
  // 读取大板块
  require '../../php/normal.php';



  function write_response_json($code, $msg="") {
    return json_encode(array('code'=>$code, 'msg'=>$msg));
  }

  function add_option_value() {
    global $conn;
    global $navs;
    // 查询arl非空字段
    $sql = "SELECT * FROM indexnav WHERE arl IS NOT NULL AND arl!=''";
    $res = $conn->query($sql);

    if($res && $res->num_rows > 0) {
      while($row = $res->fetch_assoc()) {
        $navname = $row['navname'];
        // $navs[] = array('navname'=>$navname, 'aurl'=>$row['aurl']);
        echo "<option value='".$navname."'>".$navname."</option>";
      }
    }

  }

  if(isset($_POST['navname'])) {
    $navname = $_POST['navname'];
    $arl = get_arl($navname);

    if($arl == "") {
      echo write_response_json(0, "找不到分区!");
      exit();
    }

    $sql = "SELECT * FROM nav".$arl." WHERE(arl='' AND root!=id)";
    $res = $conn->query($sql);

    $all_options = array();
    if($res && $res->num_rows) {
      while($row = $res->fetch_assoc()) {
        $all_options[] = $row['navname'];
      }
    }

    echo write_response_json(1, $all_options);
    exit();
  }

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Trisolaris 投稿(你的作品会很受欢迎哦!)</title>
  <link rel="shortcut icon" href="../../images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="../../css/general.css">
  <script src="../../js/jquery-3.3.1.js"></script>
  <script src="../../js/editor-select.js"></script>
  <style>
    .error {
      color: red;
    }
  </style>
</head>
<body>
  <div id="head">
    <h1>某无名小站<span id="strongno-rev">の</span>稿件上传区  ヾ(ω`)/ </h1>
  </div>
  <span>标题: &nbsp;</span><input type="text" id="title-input"><span id="title-error" class="error">*</span><br>
  <span>令牌: &nbsp;</span><input type="text" id="key-input" maxlength="6"><span id="key-error" class="error">*</span><br>
  <div id="editor"></div>
  <script src="../../editor/release/wangEditor.min.js"></script>
  <script src="../../js/editor.js"></script>
  <script src="../../js/contribute-index.js"></script>

  <span>提交到:&nbsp;</span>
  <select id="great-nav-select">
    <?php
      add_option_value();
    ?>
  </select>
  <select id="second-nav-select"> </select><br>
  <span>验证码:</span> <input type="text" name="authcode" id="input-authcode" maxlength="4"><span id="title-error" class="error">*</span><br>
  <img src="../../php/captcha.php?=<?php echo rand();?>" width="100" height="30" id="captcha_img">
  <button id="upload-btn">投稿</button>
</body>
</html>