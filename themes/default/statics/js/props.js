import { AlignText } from "./spin/constants.js";

export function setupWheel() {
  if (!$("#noWheel").length) {
    let props = []; // Initialize an empty array to store the props objects
    var check = $("#propsWheel").attr("data-prop").split("|");
    if (check.length > 1) {
      var proper = $("#propsWheel").attr("data-prop").split("|");
      var themess = $("#propsWheel").attr("data-themes").split("|");
      var names = $("#propsWheel").attr("data-name").split("|");
      var stores = $("#propsWheel").attr("data-store").split("|");
      var games = $("#propsWheel").attr("data-game").split("|");
      var events = $("#propsWheel").attr("data-event").split("|");
      var tokenApi = $("#tokenApi").val();
      var tokens = tokenApi.match(/\d+/g);
      // Extract the tokens from the matches
      if ($("#propsWheel").attr("data-key")) {
        var dataKey = $("#propsWheel").attr("data-key");
        var keys = dataKey.match(/\d+/g);
        var checkKey = true;
      } else {
        var key = null;
        var checkKey = false;
      }
      console.log(proper);
      for (var i = 0; i < proper.length; i++) {
        prop = JSON.parse(proper[i]);
        themes = JSON.parse(themess[i]);
        var attr = proper[i];
        var name = names[i];
        var event = events[i];
        if (checkKey) {
          var key = keys[i];
        }
        var items = $.map(prop, function (item) {
          return { label: item.prize };
        });
        var store = stores[i];
        var game = games[i];
        var token = tokens[i];
        var radius = parseFloat(themes["radius"]);
        var itemLabelRadius = parseFloat(themes["itemLabelRadius"]);
        var itemLabelRadiusMax = parseFloat(themes["itemLabelRadiusMax"]);
        var overlay = themes["overlayImage"].replace(/\\/g, "");
        if (themes["image"] == "null") {
          var image = null;
        } else {
          var image = themes["image"].replace(/\\/g, "");
        }

        props.push({
          // Add the prop object to the props array
          name: name + ": " + event,
          key: key,
          radius: radius,
          itemLabelRadius: itemLabelRadius,
          itemLabelRadiusMax: itemLabelRadiusMax,
          itemLabelRotation: 180,
          itemLabelAlign: AlignText.left,
          itemLabelBaselineOffset: -0.07,
          itemLabelFont: "monospace",
          itemLabelFontSizeMax: 20,
          itemBackgroundColors: themes["itemBackgroundColors"],
          itemLabelColors: themes["itemLabelColors"],
          rotationSpeedMax: 900,
          rotationResistance: -500,
          lineWidth: 1,
          lineColor: "#fff",
          image: image,
          overlayImage: overlay,
          items: items,
          store: store,
          game: game,
          token: token,
          attr: attr,
        });
      }
    } else {
      var name = $("#propsWheel").attr("data-name");
      var prop = JSON.parse($("#propsWheel").attr("data-prop"));
      var items = $.map(prop, function (item) {
        return { label: item.prize };
      });
      var themes = JSON.parse($("#propsWheel").attr("data-themes"));
      var radius = parseFloat(themes["radius"]);
      var itemLabelRadius = parseFloat(themes["itemLabelRadius"]);
      var itemLabelRadiusMax = parseFloat(themes["itemLabelRadiusMax"]);
      var overlay = themes["overlayImage"].replace(/\\/g, "");
      if (themes["image"] == "null") {
        var image = null;
      } else {
        var image = themes["image"].replace(/\\/g, "");
      }
      props.push({
        // Add the prop object to the props array
        name: name,
        radius: radius,
        itemLabelRadius: itemLabelRadius,
        itemLabelRadiusMax: itemLabelRadiusMax,
        itemLabelRotation: 180,
        itemLabelAlign: AlignText.left,
        itemLabelBaselineOffset: -0.07,
        itemLabelFont: "monospace",
        itemLabelFontSizeMax: 20,
        itemBackgroundColors: themes["itemBackgroundColors"],
        itemLabelColors: themes["itemLabelColors"],
        rotationSpeedMax: 900,
        rotationResistance: -500,
        lineWidth: 1,
        lineColor: "#fff",
        image: image,
        overlayImage: overlay,
        items: items,
      });
    }
    return props; // Return the props array
  } else {
    var props = false;
    return props; // Return the props array
  }
}
