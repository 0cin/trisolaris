<?php

// 以下代码来自:
// https://github.com/jiangmingkai/wangEditor-3.0.15-upload
// 感谢分享。


function mkdirs($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}

$savename = date('YmdHis',time()).mt_rand(0,9999).'.jpeg';//localResizeIMG压缩后的图片都是jpeg格式
$imgdirs = "Upload/".date('Y-m-d',time()).'/';
mkdirs($imgdirs);
$fileName = $_FILES["file"]["name"];
$savepath = 'Upload/'.date('Y-m-d' ,time()).'/'.$savename;
// $data['errno'] = 0;
$data['data'] = $savepath;
move_uploaded_file($_FILES["file"]["tmp_name"],$savepath);
print_r(json_encode($data));


?>