var player1 = '';
var player2 = '';
var currentPlayer = '';
var symbols = {};
var board = [];
var timer;
var timeLeft = 30;

async function askToStartGame() {
    player1 = document.getElementById('player1').value;
    player2 = document.getElementById('player2').value;
    if (player1 === '' || player2 === '') {
        Swal.fire({
            'title': 'Error',
            'text': 'Debes ingresar los nombres de los jugadores',
            'icon': 'error'
        });
        return;
    }
    if (player1 === player2) {
        Swal.fire({
            'title': 'Error',
            'text': 'Debes ingresar nombres diferentes',
            'icon': 'error'
        });
        return;
    }
    let res = await Swal.fire({
        'title': 'Iniciar Juego',
        'text': 'Quieres iniciar el juego o volver a ingresar los nombres de los jugadores?',
        'icon': 'question',
        'showCancelButton': true,
        'confirmButtonText': 'Iniciar',
        'cancelButtonText': 'Cambiar nombres',
        'reverseButtons': true
    });
    if (res.isDismissed && res.dismiss === Swal.DismissReason.cancel) {
        return;
    }
    if (res.isConfirmed && res.value) {
        startGame();
    }
    return;
}

function startGame() {
    document.getElementById('form').hidden = true;
    document.getElementById('game').hidden = false;
    symbols = {};
    symbols[player1] = 'O';
    symbols[player2] = 'X'
    document.querySelectorAll('.cell').forEach(function (el) {
        el.textContent = '';
        el.classList = 'cell';
    });
    currentPlayer = player1;
    updateCurrentPlayer();
    board = ["", "", "", "", "", "", "", "", ""];
    startTimer();
}

function updateCurrentPlayer() {
    document.getElementById('player').textContent = currentPlayer;
}

function checkWin() {
    let winningPatterns = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6]
    ];

    return winningPatterns.some(function (item) {
        return item.every(function (idx) {
            return board[idx] === symbols[currentPlayer];
        });
    });
}

function checkDraw() {
    return board.every(function (item) {
        return item !== '';
    })
}

function setGameOver() {
    document.querySelectorAll('.cell').forEach(function (el) {
        el.classList.add('disabled');
    });
    clearInterval(timer);
}

function startTimer() {
    timeLeft = 30;
    document.getElementById('time').style.color = '#6ee7b7';
    document.getElementById('time').innerHTML = timeLeft;
    timer = setInterval(function () {
        timeLeft--;
        document.getElementById('time').innerHTML = timeLeft;
        if (timeLeft == 20) {
            document.getElementById('time').style.color = '#fef08a';
        }
        if (timeLeft == 10) {
            document.getElementById('time').style.color = '#fca5a5';
        }
        if (timeLeft <= 0) {
            setGameOver();
            showRestartOptions(`El jugador \"${currentPlayer}\" perdió por tiempo`);
        }
    }, 1000);
}

async function showRestartOptions(message) {
    let res = await Swal.fire({
        'title': message,
        'text': 'Qué deseas hacer?',
        'showCancelButton': true,
        'confirmButtonText': 'Reiniciar',
        'cancelButtonText': 'Cambiar nombres',
        'reverseButtons': true,
        'allowOutsideClick': false,
    });
    if (res.isDismissed && res.dismiss === Swal.DismissReason.cancel) {
        document.getElementById('form').hidden = false;
        document.getElementById('game').hidden = true;
        return;
    }
    if (res.isConfirmed && res.value) {
        startGame();
    }
}

function cellClicked(cell) {
    if (cell.classList.contains('disabled')) {
        return;
    }
    cell.innerHTML = symbols[currentPlayer];
    board[cell.id] = symbols[currentPlayer];
    cell.classList.add('disabled');

    if (checkWin()) {
        setGameOver();
        showRestartOptions(`El jugador ${currentPlayer} ha gando!!`);
        return;
    } else if (checkDraw()) {
        setGameOver();
        showRestartOptions("Es un empate!!");
        return;
    }

    currentPlayer = currentPlayer == player1 ? player2 : player1;
    updateCurrentPlayer();
    clearInterval(timer);
    startTimer();
}

