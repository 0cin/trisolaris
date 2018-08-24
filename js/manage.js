$(document).ready(function () {
  // key输入框失去焦点
  $("#input-ukey").blur(function (e) {
    // e.preventDefault();
    $("#origin-username").text("");
    $.ajax({
      type: "post",
      url: "index.php",
      data: {
        ukey: $("#input-ukey").val()
      },
      dataType: "json",
      success: function (response) {
        if (response.code == 0) {
          $("#origin-username").text("无效的令牌");
        } else {
          $("#origin-username").text(response.msg);
        }
      }
    });
  });

  $("#login-btn").click(function (e) {
    // e.preventDefault();
    $("#prompt").text("");
    $.ajax({
      type: "post",
      url: "index.php",
      data: {
        origin_username: $("#origin-username").text(),
        new_username: $("#input-username").val()
      },
      dataType: "json",
      success: function (response) {
        $("#prompt").text(response.msg);

      }
    });
  });
  // 主动触发一次事件
  $("#input-ukey").blur();
});