jQuery(document).ready(function ($) {
  if ($("#wac-sortable").length) {
    $("#wac-sortable").sortable({
      axis: "y",
      cursor: "move",
      update: function () {
        var order = [];
        $("#wac-sortable li").each(function () {
          order.push($(this).data("element"));
        });
        $("#wac-element-order").val(order.join(","));
      },
    });
  }
});
