function refresh_authcode() {
  document.getElementById('captcha_img').src = '../../php/captcha.php?r=' + Math.random();
}
$(document).ready(function () {
  $("#upload-btn").click(function (e) {
    // e.preventDefault();
    var title = $("#title-input").val();
    var author = $("#key-input").val();
    var content = editor.txt.html();
    if (title == null || author == null || content == null) {
      alert("标题， 令牌， 内容不能为空!");
      return;
    }
    if (title.length == 0 || author.length == 0 || content.length == 0) {
      alert("标题， 令牌， 内容不能为空!");
      return;
    }
    $.ajax({
      type: "post",
      url: "contribute.php",
      data: {
        title: $("#title-input").val(),
        author: $("#key-input").val(),
        content: editor.txt.html(),
        firstnav: $("#great-nav-select").val(),
        secondnav: $("#second-nav-select").val(),
        authcode: $("#input-authcode").val()
      },
      dataType: "text",
      success: function (response) {
        alert(response);
        refresh_authcode();
        $.ajax({
          type: "post",
          url: "../../php/send-mail.php",
          data: {
            author: author,
            title: title
          },
          dataType: "text",
          success: function (response) {
          }
        });
      }
    });
  });
});