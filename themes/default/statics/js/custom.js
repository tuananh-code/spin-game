$(document).ready(function () {
  // console.log(window.location.href);
  $("#reload").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/spin";
  });
  $(".spin_prize").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/spin_prize";
  });
  $(".game").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/game";
  });

  $(".notify").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/notifications";
  });

  $("#redirect").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/spin_prize";
  });

  $(".add-prize").on("click", function (e) {
    e.preventDefault();
  });
  $(".add-store").on("click", function (e) {
    e.preventDefault();
  });
  $(".add-condition").on("click", function (e) {
    e.preventDefault();
  });
});
