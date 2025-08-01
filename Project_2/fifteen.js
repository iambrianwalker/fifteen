// fifteen.js

let puzzle = document.getElementById("puzzle");
let shuffleButton = document.getElementById("shuffle-button");
let saveButton = document.getElementById("save-button");

let timer = 0;
let timerId;
let moveCount = 0;

let tiles = [];
let emptyX = 3;
let emptyY = 3;
const size = 4;

function init(backgroundImageUrl = null) {
  puzzle.innerHTML = "";
  tiles = [];
  emptyX = 3;
  emptyY = 3;
  timer = 0;
  moveCount = 0;
  clearInterval(timerId);
  timerId = setInterval(() => timer++, 1000);

  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      let number = y * size + x + 1;
      if (number === size * size) break;

      let tile = document.createElement("div");
      tile.className = "tile";
      tile.innerText = number;
      tile.style.left = x * 100 + "px";
      tile.style.top = y * 100 + "px";

      if (backgroundImageUrl) {
        tile.style.backgroundImage = `url('${backgroundImageUrl}')`;
        tile.style.backgroundSize = `${size * 100}px ${size * 100}px`;
        tile.style.backgroundPosition = `-${x * 100}px -${y * 100}px`;
      } else {
        tile.style.backgroundImage = "none";
      }

      tile.dataset.x = x;
      tile.dataset.y = y;

      tile.addEventListener("click", moveTile);
      tile.addEventListener("mouseover", () => {
        if (canMove(x, y)) tile.classList.add("movablepiece");
      });
      tile.addEventListener("mouseout", () => tile.classList.remove("movablepiece"));

      tiles.push(tile);
      puzzle.appendChild(tile);
    }
  }
}

function canMove(x, y) {
  return (Math.abs(emptyX - x) + Math.abs(emptyY - y)) === 1;
}

function moveTile(e) {
  let tile = e.target;
  let x = parseInt(tile.dataset.x);
  let y = parseInt(tile.dataset.y);

  if (canMove(x, y)) {
    // Move tile visually
    tile.style.left = emptyX * 100 + "px";
    tile.style.top = emptyY * 100 + "px";

    // Swap dataset coordinates
    tile.dataset.x = emptyX;
    tile.dataset.y = emptyY;

    // Update empty tile position
    emptyX = x;
    emptyY = y;

    moveCount++;
  }
}

function shuffle() {
  for (let i = 0; i < 300; i++) {
    let neighbors = tiles.filter(t => canMove(parseInt(t.dataset.x), parseInt(t.dataset.y)));
    if (neighbors.length > 0) {
      let tile = neighbors[Math.floor(Math.random() * neighbors.length)];
      moveTile({ target: tile });
    }
  }
}

function saveGame() {
  const moves = moveCount;
  const time = timer;
  const won = isSolved();

  fetch("save_game.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `time=${time}&moves=${moves}&won=${won ? 1 : 0}`
  })
  .then(res => res.text())
  .then(msg => {
    const messageElem = document.getElementById("messages");
    if (messageElem) {
      messageElem.innerText = msg;
    } else {
      alert(msg);
    }
  });
}

function isSolved() {
  return tiles.every((tile, i) => {
    let correctX = i % size;
    let correctY = Math.floor(i / size);
    return parseInt(tile.dataset.x) === correctX && parseInt(tile.dataset.y) === correctY;
  });
}

if (shuffleButton) shuffleButton.addEventListener("click", shuffle);
if (saveButton) saveButton.addEventListener("click", saveGame);

// Initialize on page load with optional background image URL from PHP injected as window.backgroundImageUrl
window.onload = function() {
  init(window.backgroundImageUrl || null);
};
