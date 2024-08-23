$(document).ready(function () {
  $("#reload").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/subscriptions";
  });
  $(".game").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/game";
  });
  $(".store").on("click", function () {
    //upload file remove social
    window.location.href = window.location.origin + "/social/store";
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
