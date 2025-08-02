// fifteen.js

let puzzle = document.getElementById("puzzle");
let shuffleButton = document.getElementById("shuffle-button");
let saveButton = document.getElementById("save-button");

let timer = 0;
let timerId;
let moveCount = 0;
let gameWon = false;

let tiles = [];
let emptyX = 3;
let emptyY = 3;
let size = 4;

function init(backgroundImageUrl = null, customSize = null) {
  puzzle.innerHTML = "";
  if (customSize) size = customSize;
  tiles = [];
  emptyX = 3;
  emptyY = 3;
  timer = 0;
  moveCount = 0;
  gameWon = false;
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

  shuffle(); // Automatically shuffle at game start
}

function canMove(x, y) {
  return (Math.abs(emptyX - x) + Math.abs(emptyY - y)) === 1;
}

function moveTile(e, silent = false) {
  if (gameWon && !silent) return;

  let tile = e.target;
  let x = parseInt(tile.dataset.x);
  let y = parseInt(tile.dataset.y);

  if (canMove(x, y)) {
    tile.style.left = emptyX * 100 + "px";
    tile.style.top = emptyY * 100 + "px";

    tile.dataset.x = emptyX;
    tile.dataset.y = emptyY;

    emptyX = x;
    emptyY = y;

    if (!silent) moveCount++;

    if (!silent && isSolved()) {
      gameWon = true;
      clearInterval(timerId);
      alert(`You solved it in ${moveCount} moves and ${timer} seconds!`);
      saveGame();
    }
  }
}


let moveHistory = [];

function shuffle() {
  moveHistory = []; // reset

  for (let i = 0; i < 300; i++) {
    let neighbors = tiles.filter(t => canMove(parseInt(t.dataset.x), parseInt(t.dataset.y)));
    if (neighbors.length > 0) {
      let tile = neighbors[Math.floor(Math.random() * neighbors.length)];
      moveTile({ target: tile }, true); // pass flag to suppress autosave
      moveHistory.push(tile); // store move for autosolve
    }
  }
  moveCount = 0;
  timer = 0;
}

let autosolveButton = document.getElementById("autosolve-button");
if (autosolveButton) autosolveButton.addEventListener("click", autosolve);

function autosolve() {
  if (!moveHistory || moveHistory.length === 0) {
    alert("No move history available.");
    return;
  }

  let i = moveHistory.length - 1;
  let interval = setInterval(() => {
    if (i < 0) {
      clearInterval(interval);
      if (isSolved()) {
        alert("Puzzle auto-solved!");
      }
      return;
    }

    moveTile({ target: moveHistory[i] }, true); // silent
    i--;
  }, 100);
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

if (shuffleButton) shuffleButton.addEventListener("click", () => {
  init(window.backgroundImageUrl || null); // re-init with shuffle
});

if (saveButton) saveButton.addEventListener("click", saveGame);

window.onload = function () {
  const preferredSize = parseInt(window.userPreferredSize || "4");
  init(window.backgroundImageUrl || null, preferredSize);
};
