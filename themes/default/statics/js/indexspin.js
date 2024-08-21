import { Wheel } from "./spin/spin-wheel-esm.js";
import { loadFonts, loadImages } from "./spin/util.js";
var path = window.location.pathname;
// import { props } from "./propspin.js";
let props;

async function loadProps() {
  if (path.includes("game")) {
    const module = await import("./propspin.js");
    props = module.props;
  } else {
    const module = await import("./props.js");
    props = module.props;
  }
  var token = $("#tokenApi").val();
  if (token) {
    var tokenApi = token[32];
  }
  await loadFonts(props.map((i) => i.itemLabelFont));
  const wheel = new Wheel(document.querySelector(".wheel-wrapper"));
  const dropdown = document.querySelector(".themes");
  const images = [];
  for (const p of props) {
    // Initalise dropdown with the names of each example:
    //for spin
    const opt = document.createElement("option");
    opt.textContent = p.name;
    dropdown.append(opt);

    // Convert image urls into actual images:
    images.push(initImage(p, "image"));
    images.push(initImage(p, "overlayImage"));
    for (const item of p.items) {
      images.push(initImage(item, "image"));
    }
  }
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

  window.addEventListener("click", (e) => {
    // Listen for click event on spin button:
    if (e.target === btnSpin) {
      // btnSpin.disabled = true;
      // const { duration, winningItemRotaion } = calcSpinToValues();
      // wheel.spinTo(winningItemRotaion, duration);
      wheel.spinToItem(tokenApi, 4000, true, 12, 1, null);
    }
  });

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
