import { Wheel } from "./spin/spin-wheel-esm.js";
import { loadFonts, loadImages } from "./spin/util.js";
import { setupWheel } from "./props.js";

let props = setupWheel();
if (props) {
  async function loadProps() {
    // if (path.includes("game")) {
    //   const module = await import("./propspin.js");
    //   props = module.props;
    // } else {
    //   const module = await import("./props.js");
    //   props = module.props;
    // }
    var token = $("#tokenApi").val();
    if (token.length > 66) {
      var tokenApi = token[32] + token[33] + token[34];
    } else if (token.length > 65) {
      var tokenApi = token[32] + token[33];
    } else if (token) {
      var tokenApi = token[32];
    }
    function generateRandomWordAndNumber(length) {
      const characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      let randomString = "";
      for (let i = 0; i < length; i++) {
        randomString += characters.charAt(
          Math.floor(Math.random() * characters.length)
        );
      }
      return randomString;
    }

    await loadFonts(props.map((i) => i.itemLabelFont));
    const wheel = new Wheel(document.querySelector(".wheel-wrapper"));
    const dropdown = document.querySelector(".store-api");
    const images = [];

    const firstP = props[0];

    for (const p of props) {
      // Initalise dropdown with the names of each example:
      //for spin
      const opt = document.createElement("option");
      opt.textContent = p.name;
      opt.setAttribute("data-store", p.store);
      opt.setAttribute("data-game", p.game);
      opt.setAttribute("data-name", p.name);
      opt.setAttribute("data-prop", p.attr);
      if (p.key) {
        opt.setAttribute(
          "data-key",
          generateRandomWordAndNumber(64) +
            p.key +
            generateRandomWordAndNumber(64)
        );
      }
      if (props.length > 1) {
        opt.value =
          generateRandomWordAndNumber(64) +
          p.token +
          generateRandomWordAndNumber(64);
      }

      dropdown.append(opt);

      // Convert image urls into actual images:
      images.push(initImage(p, "image"));
      images.push(initImage(p, "overlayImage"));
      for (const item of p.items) {
        images.push(initImage(item, "image"));
      }
    }

    // Set the attributes on the dropdown using the first p object
    dropdown.setAttribute("data-store", firstP.store);
    dropdown.setAttribute("data-game", firstP.game);
    dropdown.setAttribute("data-name", firstP.name);
    dropdown.setAttribute("data-prop", firstP.attr);
    if (firstP.key) {
      dropdown.setAttribute(
        "data-key",
        generateRandomWordAndNumber(64) +
          firstP.key +
          generateRandomWordAndNumber(64)
      );
    }
    // console.log(firstP.name);
    $(".store-name").html(firstP.name);

    dropdown.addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const dataStore = selectedOption.dataset.store;
      const dataGame = selectedOption.dataset.game;
      const dataProp = selectedOption.dataset.prop;
      const dataName = selectedOption.dataset.name;
      if (selectedOption.dataset.key) {
        const dataKey = selectedOption.dataset.key;
        dropdown.setAttribute("data-key", dataKey);
      }
      // Update the data-store attribute of the dropdown itself
      dropdown.setAttribute("data-store", dataStore);
      dropdown.setAttribute("data-game", dataGame);
      dropdown.setAttribute("data-prop", dataProp);
      dropdown.setAttribute("data-name", dataName);

      $(".store-name").html(dataName);
    });

    await loadImages(images);

    // Show the wheel once everything has loaded
    document.querySelector(".wheel-wrapper").style.visibility = "visible";

    // Handle dropdown change:
    dropdown.onchange = () => {
      wheel.init({
        ...props[dropdown.selectedIndex],
        rotation: wheel.rotation, // Preserve value.
      });
    };
    // Select default:
    dropdown.options[0].selected = "selected";
    dropdown.onchange();

    // Save object globally for easy debugging.
    window.wheel = wheel;

    const btnSpin = document.querySelector(".spin");
    let modifier = 0;
    if (props.length > 1) {
      window.addEventListener("click", (e) => {
        // Listen for click event on spin button:
        if (e.target === btnSpin) {
          // btnSpin.disabled = true;
          // const { duration, winningItemRotaion } = calcSpinToValues();
          // wheel.spinTo(winningItemRotaion, duration);
          if (props.length > 1) {
            var tokenApi = document.querySelector(".store-api").value[64];
          }
          // console.log(tokenApi);
          var themes = document.getElementById("themes");
          var keyCheck = themes.getAttribute("data-key")[64];
          if (keyCheck > 0) {
            wheel.spinToItem(tokenApi, 4000, true, 12, 1, null);
          }
        }
      });
    } else {
      window.addEventListener("click", (e) => {
        if (e.target === btnSpin) {
          // btnSpin.disabled = true;
          // console.log(tokenApi);
          var themes = document.getElementById("themes");
          var keyCheck = themes.getAttribute("data-key")[64];
          if (keyCheck > 0) {
            wheel.spinToItem(tokenApi, 4000, true, 12, 1, null);
          }
        }
      });
    }

    function calcSpinToValues() {
      const duration = 4000;
      // const winningItemRotaion = getRandomInt(360, 360 * 8) + 360 * 8;
      //value from 0 to 360
      const winningItemRotaion = 135 + 360 * 12;
      // modifier += 360 * 4;
      return { duration, winningItemRotaion };
    }

    function getRandomInt(min, max) {
      min = Math.ceil(min);
      max = Math.floor(max);
      console.log(Math.random() * (max - min));
      return Math.floor(Math.random() * (max - min)) + min;
    }

    function initImage(obj, pName) {
      if (!obj[pName]) return null;
      const i = new Image();
      i.src = obj[pName];
      obj[pName] = i;
      return i;
    }
  }
  loadProps();
}
// loadProps(() => {
//   var token = $("#tokenApi").val();
//   if (token) {
//     var tokenApi = token[32];
//   }
//   window.onload = async () => {
//     console.log(props);
//     await loadFonts(props.map((i) => i.itemLabelFont));
//     const wheel = new Wheel(document.querySelector(".wheel-wrapper"));
//     const dropdown = document.querySelector(".themes");
//     const images = [];
//     for (const p of props) {
//       // Initalise dropdown with the names of each example:
//       const opt = document.createElement("option");
//       opt.textContent = p.name;
//       dropdown.append(opt);

//       // Convert image urls into actual images:
//       images.push(initImage(p, "image"));
//       images.push(initImage(p, "overlayImage"));
//       for (const item of p.items) {
//         images.push(initImage(item, "image"));
//       }
//     }
//     await loadImages(images);

//     // Show the wheel once everything has loaded
//     document.querySelector(".wheel-wrapper").style.visibility = "visible";

//     // Handle dropdown change:
//     dropdown.onchange = () => {
//       wheel.init({
//         ...props[dropdown.selectedIndex],
//         rotation: wheel.rotation, // Preserve value.
//       });
//     };
//     // Select default:
//     dropdown.options[0].selected = "selected";
//     dropdown.onchange();

//     // Save object globally for easy debugging.
//     window.wheel = wheel;

//     const btnSpin = document.querySelector(".spin");
//     let modifier = 0;

//     window.addEventListener("click", (e) => {
//       // Listen for click event on spin button:
//       if (e.target === btnSpin) {
//         // btnSpin.disabled = true;
//         // const { duration, winningItemRotaion } = calcSpinToValues();
//         // wheel.spinTo(winningItemRotaion, duration);
//         wheel.spinToItem(tokenApi, 4000, true, 12, 1, null);
//       }
//     });

//     function calcSpinToValues() {
//       const duration = 4000;
//       // const winningItemRotaion = getRandomInt(360, 360 * 8) + 360 * 8;
//       //value from 0 to 360
//       const winningItemRotaion = 135 + 360 * 12;
//       // modifier += 360 * 4;
//       return { duration, winningItemRotaion };
//     }

//     function getRandomInt(min, max) {
//       min = Math.ceil(min);
//       max = Math.floor(max);
//       console.log(Math.random() * (max - min));
//       return Math.floor(Math.random() * (max - min)) + min;
//     }

//     function initImage(obj, pName) {
//       if (!obj[pName]) return null;
//       const i = new Image();
//       i.src = obj[pName];
//       obj[pName] = i;
//       return i;
//     }
//   };
// });
