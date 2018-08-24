function refresh_authcode() {
  document.getElementById('captcha_img').src = '../../php/captcha.php?r=' + Math.random();
}
$(document).ready(function () {
  $("#regbtn").click(function (e) {
    $(".error").text("");
    $.ajax({
      type: "post",
      url: "index.php",
      data: {
        authcode: $("#input-authcode").val(),
        username: $("#input-username").val()
      },
      dataType: "json",
      success: function (response) {
        // 注册失败
        if (response.code == 0) {
          $(".error").text(response.msg);
        } else {
          alert("恭喜你注册♂成功, 你的令牌为" + response.ukey);
          $(".error").text(response.ukey);
        }
        refresh_authcode();
      }

    });

  });
});
