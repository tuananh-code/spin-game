import { AlignText } from "./spin/constants.js";

export const props = [
  {
    name: "Workout",
    radius: 0.84,
    itemLabelRadius: 0.93,
    itemLabelRadiusMax: 0.35,
    itemLabelRotation: 180,
    itemLabelAlign: AlignText.left,
    itemLabelColors: ["#fff"],
    itemLabelBaselineOffset: -0.07,
    itemLabelFont: "system-ui",
    itemLabelFontSizeMax: 20,
    itemBackgroundColors: [
      "#ffc93c",
      "#66bfbf",
      "#a2d5f2",
      "#515070",
      "#43658b",
      "#ed6663",
      "#d54062",
    ],
    rotationSpeedMax: 500,
    rotationResistance: -100,
    lineWidth: 1,
    lineColor: "#fff",
    image: "./themes/default/statics/img/example-0-image.svg",
    overlayImage: "./themes/default/statics/img/example-0-overlay.svg",
    items: [
      {
        label: "TWISTS",
      },
      {
        label: "PRESS UPS",
      },
      {
        label: "JOGGING",
      },
      {
        label: "SQUATS",
      },
      {
        label: "PLANKS",
      },
      {
        label: "LUNGES",
      },
      {
        label: "BURPIES",
      },
      {
        label: "CRUNCHES",
      },
      {
        label: "MOUNT. CLIMB",
      },
      {
        label: "STAR JUMPS",
      },
      {
        label: "KANGAROOS",
      },
      {
        label: "ROPE CLIMB",
      },
      {
        label: "KICK BOXING",
      },
      {
        label: "WALL SIT",
      },
    ],
  },

  {
    name: "Movies",
    radius: 0.88,
    itemLabelRadius: 0.92,
    itemLabelRadiusMax: 0.4,
    itemLabelRotation: 180,
    itemLabelAlign: AlignText.left,
    itemLabelBaselineOffset: -0.07,
    itemLabelFont: "monospace",
    itemLabelFontSizeMax: 20,
    itemBackgroundColors: ["#c7160c", "#fff"],
    itemLabelColors: ["#fff", "#000"],
    rotationSpeedMax: 900,
    rotationResistance: -500,
    lineWidth: 1,
    image: null,
    lineColor: "#fff",
    image: null,
    overlayImage: "./themes/default/statics/img/example-2-overlay.svg",
    items: [
      {
        label: "Action",
      },
      {
        label: "Horror",
      },
      {
        label: "Science Fict.",
      },
      {
        label: "Comedy",
      },
      {
        label: "Romance",
      },
      {
        label: "Thriller",
      },
      {
        label: "Western",
      },
      {
        label: "Indie",
      },
      {
        label: "Crime",
      },
      {
        label: "Documentary",
      },
      {
        label: "Drama",
      },
      {
        label: "Musical",
      },
      {
        label: "Mystery",
      },
      {
        label: "War",
      },
      {
        label: "Sports",
      },
      {
        label: "Fantasy",
      },
    ],
  },
];
