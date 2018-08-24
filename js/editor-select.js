$(document).ready(function () {
  $("#great-nav-select").change(function (e) {
    $.ajax({
      type: "post",
      url: "index.php",
      data: {
        navname: $(this).val()
      },
      dataType: "json",
      success: function (response) {
        if (response.code == 1) {
          response.msg.forEach(function (item) {
            $("#second-nav-select").append(
              '<option value="' + item + '">' + item + '</option>'
            );
          });
        }
      }
    });
  });
  // 主动触发事件
  // 等同于$("#great-nav-select").trigger("change");
  $("#great-nav-select").change();
});