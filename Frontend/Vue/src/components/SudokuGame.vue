<template>
  <div class="WholePage">
    <div v-if="!gameOver && !gameWin">
      <header>
        <div class="difficulty">
          <p>Difficulty: </p>
          <button v-for="level in levels" :key="level" :class="{ active: difficulty === level }" @click="setDifficulty(level)">
            {{ level }}
          </button>
        </div>
        <!-- Game Status -->
        <div class="status">
          <p :class="{'red': isRed}">Score: {{ counter }}</p>
          <span>Mistakes: {{ Mistakes }}/{{ MaxMistakes }} </span> &nbsp;
          <span>Undo: {{ undotimes }}/ 2 </span> &nbsp;
          <span>Hints: {{ hinTimes }}/ {{ MaxHint }} </span> &nbsp;
          <span>{{ formatTime(timer) }}</span>
          <img src="/icons/pause-play.png" alt="Resume/Pause" class="resume-pause-logo" @click="toggleTimer" />
        </div>

        <!-- Add Button to Fetch Top Scores -->
        <button @click="fetchTopScores" class="fetch-scores-btn">Show Top Scores</button>

        <!-- Top Scores List -->
        <div v-if="topScores && topScores.length > 0" class="scores-list">
  <h3>Top Scores:</h3>
  <ul>
    <li v-for="(score, index) in topScores" :key="score.id">
      Rank {{ index + 1 }}: {{ score.username || 'Unknown' }} - {{ score.score || 0 }}
    </li>
  </ul>
</div>

<div v-else class="no-scores">
  <p>No scores available. Play the game to generate some!</p>
</div>
      </header>

      <div id="game-container">
        <!-- Sudoku Grid -->
        <div id="sudoku-wrapper">
          <div id="blur-overlay" v-if="paused">
            <img src="/icons/play-button.png" alt="Resume" class="resume-button" @click="toggleTimer" />
          </div>
          <div id="sudoku">
            <div v-for="(row, i) in board" :key="i" class="parentCube">
              <div v-for="(cell, j) in row" :key="j" class="childCube" @click="setActiveCell(i, j)">
                <div v-if="cell !== null" class="inputCell">{{ cell }}</div>
                <input
                  v-else
                  v-model.number="board[i][j]"
                  class="emptyCell"
                  type="tel"
                  @input="validateInput(i, j)"
                  maxlength="1"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Controls -->
        <div class="controls">
          <div class="control-button" @click="undo">
            <img src="/icons/undo.png" />
            <span>Undo</span>
          </div>
          <div class="control-button" @click="onEraseClick">
            <img src="/icons/erase.png" />
            <span>Erase</span>
          </div>
          <div class="control-button" @click="hint">
            <img src="/icons/hint.png" />
            <span>Hint</span>
          </div>
        </div>
        <div id="keypad">
          <button v-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="num" @click="selectNumber(num)" class="keypad-button">
            {{ num }}
          </button>
        </div>
        <button class="new-game" @click="resetGame">New Game</button>
      </div>
    </div>

    <div v-else-if="gameOver" class="game-over">
      <h3>You lost!</h3>
      <button class="resetGame" @click="resetGame">Try Again</button>
    </div>

    <div v-else-if="gameWin" class="game-win">
      <h3>Congratulations, You Won! 🎉</h3>
      <button class="resetGame" @click="resetGame">Try Again with Harder Level ;)</button>
    </div>
  </div>
</template>

<script>

export default {
  name: "SudokuGame",
  data() {
    return {
      levels: ["Easy", "Medium", "Hard"],
      board: Array.from({ length: 9 }, () => Array(9).fill(null)), // Default empty 9x9 grid with null for empty cells
      topScores: [], // Initialize top scores
      HiddenValue: Array.from({ length: 9 }, () => Array(9).fill(null)),
      difficulty: 'Easy', // Default difficulty
      selectedNumber: null, // Number selected from keypad
      activeCell: { row: null, col: null }, // Coordinates of the currently selected cell
      availabilityStatus: Array.from({ length: 9 }, () => Array(9).fill(false)),
      counter : 0,
      timer: 0,
      paused: false,
      timerInterval: null,
      notesMode: false,
      Mistakes:0,
      MaxMistakes:7, //Default Easy Mistakes,
      solvedBoard: [], // Store the solved version of the board for hint
      previousMoves: [], // Stack of previous board states for undo
      isRed: false,
      gameOver:false,
      gameWin: false, // New flag for game win status
      undotimes:2,
      hinTimes:5,
      MaxHint:5,//default for easy
    };
  },
  mounted() {
    this.fetchUserData(); // Fetch the data when the component is mounted
    this.setDifficulty(this.difficulty);
    this.setMaxMistakes(this.MaxMistakes);
    window.addEventListener('keydown', this.handleKeyboardInput);
    this.startTimer();
    this.setMaxhint(this.MaxHint);
    this.setHint(this.hinTimes);

  },
  methods: {
    getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
      return null;
    },
    fetchUserData() {
      fetch("http://127.0.0.1:8000/api/users/retrieve", {
  method: "GET",
  headers: {
    "Content-Type": "application/json",
    Authorization: `Bearer ${this.getCookie("auth_token")}`,
  },
  credentials: "include", // Ensures cookies are included
})
  .then((response) => {
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json();
  })
  .then((json) => {
    this.userData = json; // Store the fetched data
    console.log("Fetched user data:", this.userData);
  })
  .catch((error) => {
    console.error("Error fetching user data:", error);
  });
},
fetchTopScores() {
  console.log("fetchTopScores method called"); // Debugging log
  fetch("http://127.0.0.1:8000/api/users/scores/top", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${this.getCookie("auth_token")}`,
    },
    credentials: "include",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Fetched top scores:", data); // Debugging log
      this.topScores = data;
    })
    .catch((error) => {
      console.error("Error fetching top scores:", error);
    });
},

    generateNumbers() {
      const numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9];
      this.shuffle(numbers); // Shuffle numbers to ensure randomness

      let board = Array.from({ length: 9 }, () => Array(9).fill(null));
      if (this.fillBoard(board, numbers)) {
        this.solvedBoard = JSON.parse(JSON.stringify(board)); // Save a copy of the solved grid
        return board;
      } 
      else {
        return null;
      }
    },

    fillBoard(board, numbers) {
      for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
          if (board[row][col] === null) {
            for (let num of numbers) { // Use shuffled numbers
              if (this.isValid(board, row, col, num)) {
                board[row][col] = num;
                if (this.fillBoard(board, numbers)) {
                  return true;
                }
                board[row][col] = null;
              }
            }
            return false; // Backtrack if no valid number found
          }
        }
      }
      return true; // Successfully filled board
    },

    shuffle(array) {
      for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
      }
    },

    isValid(board, row, col, num) {
      for (let i = 0; i < 9; i++) {
        if (board[row][i] === num || board[i][col] === num) return false;
      }
      let startRow = row - (row % 3);
      let startCol = col - (col % 3);
      for (let i = 0; i < 3; i++) {
        for (let j = 0; j < 3; j++) {
          if (board[i + startRow][j + startCol] === num) return false;
        }
      }
      return true;
    },

    isAvailable(i, j) {
     return this.board[i][j] === null;
    },

    removeCells(difficulty) {
      let cellsToRemove = difficulty === "Easy" ? 30 : difficulty === "Medium" ? 40 : 50;
      let removedCells = 0;
      while (removedCells < cellsToRemove) {
        let row = Math.floor(Math.random() * 9);
        let col = Math.floor(Math.random() * 9);
        if (this.board[row][col] !== null) {
          this.HiddenValue[row][col] = this.board[row][col];
          this.board[row][col] = null;
          this.availabilityStatus[row][col]=true;     
          removedCells++;
        }
      }
    },

    MaxMistakesCondition()
    {
      if (this.difficulty === 'Easy'){
        this.MaxMistakes=7;
      }
      if(this.difficulty==='Medium'){
        this.MaxMistakes=5;
      }
      if(this.difficulty==='Hard'){
        this.MaxMistakes= 3;
      }
    },
    MaxHintsCondition()
    {
      if (this.difficulty === 'Easy'){
        this.MaxHint=5;
        this.hinTimes=5;
      }
      if(this.difficulty==='Medium'){
        this.MaxHint=3;
        this.hinTimes=3;
      }
      if(this.difficulty==='Hard'){
        this.MaxHint= 2;
        this.hinTimes=2;
      }
    },

    setDifficulty(difficulty) {
      this.difficulty = difficulty;
      this.counter = 0; // Reset score
      this.Mistakes = 0; // Reset mistakes
      this.gameOver = false; // Reset game over status
      this.isRed=false;
      this.board = this.generateNumbers();
      this.removeCells(this.difficulty);
      this.MaxMistakesCondition();
      this.gameWin = false; // Reset win status
      this.resetTimer();
      this.MaxHintsCondition();
    },

    setMaxMistakes(MaxMistakes){
      
      this.MaxMistakes=MaxMistakes;
    },
    setMaxhint(MaxHint){
      this.MaxHint=MaxHint;
    },
    setHint(hinTimes){
      this.hinTimes=hinTimes;
    },

    validateInput(row, col) {
      const value = this.board[row][col];
      if (value < 1 || value > 9 || isNaN(value)) {
        this.board[row][col] = null;
      }
    },

    selectNumber(num) {
      if (!this.gameOver && this.activeCell.row !== null && this.activeCell.col !== null) {
        const row = this.activeCell.row;
        const col = this.activeCell.col;

        this.updateCell(row, col, num);
      }
    },
   
    setActiveCell(row, col) {
      if (!this.gameOver && !this.gameWin && this.availabilityStatus[row][col] === true) {
        this.activeCell = { row, col };
      }
    },
    
    handleKeyboardInput(event) {
      if (this.activeCell.row === null || this.activeCell.col === null) {
        return; // No cell is selected, ignore keyboard input
      }

      const row = this.activeCell.row;
      const col = this.activeCell.col;

      // Check if a number key was pressed
      if (event.key >= 1 && event.key <= 9) {
        const inputNumber = parseInt(event.key);

        // Update the board
        this.board[row][col] = inputNumber;
        this.activeCell.row=null;
        this.activeCell.col=null;
        
        

        // Check if the input is correct
        if (this.board[row][col] === this.HiddenValue[row][col]) {
          this.counter += 10;  // Increase counter if values match
          this.isRed=false;
        } else {
          this.counter -= 10;  // Decrease counter if values don't match
          this.Mistakes += 1;  // Increment mistakes if values don't match
          this.isRed=true;
          this.GameFinish();    // Check if the game should end
        }
      } else if (event.key === 'Backspace' || event.key === 'Delete') {
        // Handle cell erase
        if (this.availabilityStatus[row][col] === true) {
          this.board[row][col] = null;
        }
      }
      this.checkWin();
    },

    updateCell(row, col, value) {
      // Save current state before change
      this.previousMoves.push({
        board: JSON.parse(JSON.stringify(this.board)),
        mistakes: this.Mistakes,
        score: this.counter
      });

      this.board[row][col] = value;
      this.activeCell.row=null;
      this.activeCell.col=null;

      if (this.board[row][col] === this.HiddenValue[row][col]) {
        this.counter += 10;
        this.isRed = false;
      } else {
        this.counter -= 10;
        this.Mistakes += 1;
        this.isRed = true;
        this.GameFinish();
      }
      
      this.checkWin(); // Check for win after updating the cell
    },

    // Starts the timer
    startTimer() {
        // Ensure no previous interval exists
        if (!this.timerInterval) {
          this.timerInterval = setInterval(() => {
            if (!this.paused) {
              this.timer++;
            }      
          }, 1000);
        }
      },

    // Toggles the timer between running and paused
    toggleTimer() {
      if (this.timerInterval) {
        clearInterval(this.timerInterval); // Stop the timer
        this.timerInterval = null;
        this.paused = true;
      } else {
        this.startTimer(); // Restart the timer
        this.paused = false;
      }
    },

    // Resets the timer to 0 and stops the interval
    resetTimer() {
      // Clear the current interval
      if (this.timerInterval) {
        clearInterval(this.timerInterval);
        this.timerInterval = null;
      }

      // Reset the timer value to 0
      this.timer = 0;

      // Optionally, restart the timer if needed
      this.startTimer();
    },
  
    formatTime(time) {
      const minutes = String(Math.floor(time / 60)).padStart(2, '0');
      const seconds = String(time % 60).padStart(2, '0');
      return `${minutes}:${seconds}`;
    },

   // Handle cell erasing for keyboard and button erase
    onEraseClick() {
      if (!this.gameOver && this.activeCell.row !== null && this.activeCell.col !== null) {
        const row = this.activeCell.row;
        const col = this.activeCell.col;

        if (this.availabilityStatus[row][col] === true) {
          this.board[row][col] = null;
        }
      }
    },

    resetGame() {
      this.board = Array.from({ length: 9 }, () => Array(9).fill(null));
      this.HiddenValue = Array.from({ length: 9 }, () => Array(9).fill(null));
      this.Mistakes = 0;
      this.counter = 0;
      this.gameOver = false;
      this.gameWin = false; // Reset win status
      this.setDifficulty(this.difficulty); // Reinitialize the board based on difficulty
      this.resetTimer();
      this.undotimes=2;
      
      
    },

    GameFinish(){
      if(this.Mistakes===this.MaxMistakes){
        this.playLoseSound();
        this.gameOver = true;
      }
    }, 

    checkWin() {
     let isWon = true;

      for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
          // Check only cells that were originally hidden and filled by the player
          if (this.HiddenValue[row][col] !== null && this.board[row][col] !== this.HiddenValue[row][col]) {
            isWon = false;
            break;
          }
        }
        if (!isWon) break;
      }

      if (isWon) {
        this.gameWin = true;
        this.playWinSound();
        console.log("Congratulations! You've completed the puzzle.");
      }
    },

/*    toggleNotes() {
        this.notesMode = !this.notesMode;
        },
*/
    undo() {
      if (this.previousMoves.length > 0 && this.undotimes>0) {
        const lastMove = this.previousMoves.pop();
        this.board = lastMove.board;
        this.Mistakes = lastMove.mistakes;
        this.counter = lastMove.score;
        this.isRed = false; // Reset red highlight if undo corrects a mistake
        this.undotimes--;
      }
    },


    hint() {
      let emptyCells = [];

      // Collect all empty cells
      for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
          if (this.board[row][col] === null) {
            emptyCells.push({ row, col });
          }
        }
      }

      if (emptyCells.length > 0 && this.hinTimes>0) {
        // Pick a random empty cell
        const randomIndex = Math.floor(Math.random() * emptyCells.length);
        const { row, col } = emptyCells[randomIndex];

        // Fill it with the correct value from the solved grid
        this.board[row][col] = this.solvedBoard[row][col];
        this.hinTimes--
        
      }
    },


    // Ensure cleanup of the event listener
    beforeDestroy() {
      window.removeEventListener('keydown', this.handleKeyboardInput);
    }, 
  
    playWinSound() {
      const winSound = new Audio('/sounds/winSound.wav'); // Path to your win sound
      winSound.play();
    },

    playLoseSound() {
        const loseSound = new Audio('/sounds/loseSound.wav'); // Path to your lose sound
        loseSound.play();
    },
}
}
</script>

<style scoped>
.WholePage {
 
  width: 100vw; /* Full viewport width */
  height: 100vh; /* Full viewport height */
  display: flex;
  justify-content: center;
  align-items: center;
}
header {
  display: flex;
  justify-content: space-between;
  width: 100%;
  padding: 1rem;
  font-size: 1.2rem;
  font-weight: bold;
  color: #555;
}

body {
  font-family: 'Roboto', sans-serif; /* Clean, modern font */
  color: #333;
  background-color: #f4f4f9; /* Light background to reduce eye strain */
}

header, .difficulty, .controls {
  font-family: 'Montserrat', sans-serif; /* More structured and bold font for headers */
  font-weight: 600;
}

.control-button span, .difficulty p {
  font-size: 1.1rem;
}

.inputCell, .keypad-button {
  font-family: 'Courier New', monospace; /* Use a monospace font for the grid for consistency */
}

/**************************/

.difficulty button {
  background-color: #e0e0e0;
  border: none;
  border-radius: 5px;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.difficulty button:hover {
  background-color: #555;
}

.difficulty button.active {
  font-weight: bold;
  background-color: #333;
  color: white;
}

/**************************/

.controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px; /* Space between each button */
  margin-top: 20px;
}

.control-button {
  display: flex;
  flex-direction: column; /* Arrange icon and text vertically */
  align-items: center;
  text-align: center;
  cursor: pointer;
}

.control-button img {
  width: 45px; /* Set the size of the circular button */
  height: 45px;
  border-radius: 50%; /* Make the image circular */
  background-color: #ddd; /* Optional: Background for buttons */
  display: flex;
  justify-content: center;
  align-items: center;
  transition: transform 0.2s ease; /* Smooth scaling effect */
  object-fit: scale-down;
}

.control-button img:hover {
  transform: scale(1.06); /* Slight zoom on hover */
}

.control-button span {
  margin-top: 3px; /* Space between the icon and text */
  font-size: 1rem;
  color: #333;
  font-weight: bold;
}

/**************************/

.resume-pause-logo {
  width: 16px; /* Adjust width */
  height: 16px; /* Adjust height */
  cursor: pointer;
  margin-left: 5px;
  margin-bottom: 4px;
  transition: transform 0.2s ease; /* Add hover effect */
}

.resume-pause-logo:hover {
  transform: scale(1.06); /* Slight zoom on hover */
}

/**************************/

#game-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
}

#sudoku-wrapper {
  position: relative;
  display: inline-block;
}

#blur-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10; /* Ensure it is above the grid */
  backdrop-filter: blur(5px); /* Blur the grid */
  pointer-events: all; /* Prevent interaction with blurred grid */
}

.resume-button {
  width: 32px; /* Adjust size as needed */
  height: 32px;
  cursor: pointer;
  pointer-events: auto; /* Enable interaction */
  transition: transform 0.2s ease; /* Add hover effect */
}

.resume-button:hover {
  transform: scale(1.1); /* Slight zoom on hover */
}

/**************************/

#keypad {
  display: grid;
  grid-template-columns: repeat(3, 50px);
  gap: 2px;
}

.keypad-button {
  width: 50px;
  height: 50px;
  font-size: 1.5rem;
  text-align: center;
  background-color: #ccc;
  border: 1px solid #333;
  cursor: pointer;
}

.keypad-button:hover {
  background-color: #aaa;
}

.keypad-button:active {
  transform: scale(0.9);
}

#sudoku {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 5px;
  width: 525px;
  height: 525px;
  border: 4px solid #000000;
  background-color: #333;
}

.parentCube {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1px;
  width: 100%;
  height: 100%;
  background-color: #0d0d0d;
  box-sizing: border-box;
}

.childCube {
  width: 100%;
  height: 100%;
  background-color: #f0f0f0;
  border: 1px solid #9f9f9f;
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
  cursor: pointer;
}

.emptyCell {
  width: 100%;
  height: 100%;
  border: none;
  text-align: center;
  font-size: 1.5rem;
  background-color: #f0f0f0;
}
.red {
  color: red;
}
.game-over{
  color:red;
}
.game-win{
  color:green;
}
.Erase , .resetGame{
  background-color: #9f9f9f; /* Green background */
  border: none;
  color: rgb(1, 1, 1);
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  font-weight: bold;
  margin: 4px 2px;
  cursor: pointer;
  border-radius: 8px; /* Rounded corners */
  transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth transitions */
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); /* Shadow for depth */
}

.Erase:hover ,.resetGame:hover {
  background-color: #9f9f9f; /* Darker green on hover */
  transform: scale(1.05); /* Slightly larger on hover */
}

.Erase:active ,.resetGame:active{
  background-color: #9f9f9f; /* Even darker green on click */
  transform: scale(1); /* Return to original size */
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2); /* Reduced shadow on click */
}
.new-game {
  background-color: #e0e0e0;
  color: #000;
  border: none;
  border-radius: 5px;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  margin-top: 1rem;
}

.new-game:hover {
  background-color: #555;
}

.new-game:active {
  transform: scale(0.95);
}

@keyframes winAnimation {
  0% { transform: scale(1); opacity: 0; }
  50% { transform: scale(1.2); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}

.game-win {
  color: green;
  animation: winAnimation 1s ease-in-out;
  font-size: 2rem;
  text-align: center;
}

@keyframes loseAnimation {
  0% { transform: scale(1); opacity: 0; }
  50% { transform: scale(1.2); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}

.game-over {
  color: red;
  animation: loseAnimation 1s ease-in-out;
  font-size: 2rem;
  text-align: center;
}


/* Media Queries */
@media screen and (max-width: 768px) {
  #sudoku {
    max-width: 400px; /* Smaller size for tablets */
    gap: 4px;
  }

  #keypad {
    grid-template-columns: repeat(3, 40px);
    gap: 2px;
  }

  .keypad-button {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }

  .control-button img {
    width: 40px;
    height: 40px;
  }

  .control-button span {
    font-size: 0.9rem;
  }
}

@media screen and (max-width: 480px) {
  header {
    flex-direction: column;
    align-items: center;
    font-size: 1rem;
  }

  #sudoku {
    max-width: 300px; /* Smaller size for mobile */
    gap: 3px;
  }

  #keypad {
    grid-template-columns: repeat(3, 35px);
    gap: 1px;
  }

  .keypad-button {
    width: 35px;
    height: 35px;
    font-size: 1rem;
  }

  .control-button img {
    width: 35px;
    height: 35px;
  }

  .control-button span {
    font-size: 0.8rem;
  }
}

@media screen and (max-width: 320px) {
  #sudoku {
    max-width: 250px;
    gap: 2px;
  }

  #keypad {
    grid-template-columns: repeat(3, 30px);
    gap: 1px;
  }

  .keypad-button {
    width: 30px;
    height: 30px;
    font-size: 0.9rem;
  }

  .control-button img {
    width: 30px;
    height: 30px;
  }

  .control-button span {
    font-size: 0.7rem;
  }
}

</style>