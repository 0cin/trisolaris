<?php

// 以下代码摘抄自https://blog.csdn.net/zhihua_w/article/details/52798896
// 作者写的很好。 基本上抄了一遍， 学到了不少东西， 感谢分享， 保护作者版权。

// sunshine+ice 2018-8-22

$captch_code_size = 4;

// 开启session
// session_start();

// 创建一个100*30的画布
$image = imagecreatetruecolor(100, 30);
// 白色背景
$bgcolor = imagecolorallocate($image, 255, 255, 255);
// 将白色背景填充画布
imagefill($image, 0, 0, $bgcolor);

$captch_code = '';

for($i = 0; $i < $captch_code_size; $i++) {
  $fontsize = 10;
  // 随机字体颜色
  $fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));
  // 随机抽取一个字符, 排除了l和1等坑人的字符
  $data = 'abcdefghijkmnpqrstuvwxy23456789';
  $fontcontent = substr($data, rand(0, strlen($data) - 1), 1);
  $captch_code .= $fontcontent;
  // 分配随机坐标
  $x = ($i * 100 / 4) + rand(5, 10);
  $y = rand(5, 10);
  imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
}
session_start();
// 将生成的验证码保存到session
$_SESSION['authcode'] = $captch_code;

// 在图片上瞎几把画些点
for($i = 0; $i < 200; $i++) {
  $pointcolor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
  imagesetpixel($image, rand(1, 99), rand(1, 29), $pointcolor);
}

// 瞎几把画线
for($i = 0; $i < 3; $i++) {
  $linecolor = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220));
  imageline($image, rand(1, 99), rand(1, 29), rand(1, 99), rand(1, 29), $linecolor);
}

// 设置头
header('content-type:image/png');
imagepng($image);
imagedestory($image);

?>