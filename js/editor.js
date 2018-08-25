
var E = window.wangEditor;
var editor = new E("#editor");
editor.customConfig.uploadImgServer = 'upload.php'
editor.customConfig.uploadImgMaxSize = 0.5 * 1024 * 1024;
editor.customConfig.uploadImgMaxLength = 15;
editor.customConfig.uploadFileName = 'file';
editor.customConfig.uploadImgHeaders = {
  'Accept': 'multipart/form-data'
};
editor.customConfig.uploadImgHooks = {
  error: function (xhr, editor) {
    alert("2:" + xhr);
  },
  fail: function (xhr, editor, result) {
    alert("1:" + xhr);
  },
  success: function (xhr, editor, result) {

  },
  customInsert: function (insertImg, result, editor) {
    console.log(result);
    insertImg(result.data);
  }
};
editor.create();