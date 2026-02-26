<x-shop::layouts>
    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <style>
            .tetris-page-container {
                background-color: #050505;
                min-height: 90vh;
                font-family: 'Press Start 2P', cursive;
                /* True 8-bit font */
                background-image:
                    linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%),
                    linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
                background-size: 100% 2px, 3px 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            :root {
                --neon-pink: #ff00ff;
                --neon-cyan: #00ffff;
                --neon-yellow: #ffff00;
                --neon-green: #39ff14;
            }

            .arcade-cabinet {
                display: flex;
                flex-direction: column;
                lg: flex-direction: row;
                background: #111;
                border: 4px solid #333;
                border-radius: 4px;
                box-shadow:
                    0 0 20px rgba(0, 255, 255, 0.2),
                    inset 0 0 50px rgba(0, 0, 0, 0.8);
                overflow: hidden;
                max-width: 900px;
                width: 100%;
                border-top: 10px solid #222;
                /* Cabinet header feel */
            }

            /* LEFT SIDE: GAME */
            .game-section {
                flex: 1;
                padding: 40px;
                background: #000;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                border-right: 4px solid #333;
                position: relative;
            }

            .crt-overlay {
                background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
                background-size: 100% 4px, 6px 100%;
                pointer-events: none;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 5;
                opacity: 0.2;
            }

            canvas {
                border: 4px solid #333;
                box-shadow: 0 0 0 2px #555;
                /* Double border effect */
                background-color: #000;
                image-rendering: pixelated;
                /* Crisp pixels */
            }

            /* RIGHT SIDE: LEADERBOARD */
            .leaderboard-section {
                width: 100%;
                lg:width: 280px; /* Reduced from 380px */
                background: #000;
                padding: 15px;
                border-left: 2px solid #222;
                color: var(--neon-cyan);
                display: flex;
                flex-direction: column;
                font-family: 'Press Start 2P', cursive;
            }

            .retro-title {
                font-size: 0.8rem; /* Smaller */
                line-height: 1.5;
                text-align: center;
                color: var(--neon-pink);
                text-shadow: 2px 2px 0px #000;
                margin-bottom: 30px;
                text-transform: uppercase;
            }

            .score-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .score-item {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                /* Align dots better */
                padding: 8px 0;
                font-size: 0.6rem;
                /* Even smaller */
                line-height: 1;
            }

            .score-separator {
                flex-grow: 1;
                border-bottom: 2px dotted #333;
                margin: 0 5px 3px 5px;
                /* Align dots */
            }

            .score-item:first-child {
                color: var(--neon-yellow);
            }

            .score-item:nth-child(2) {
                color: #fff;
            }

            .score-item:nth-child(3) {
                color: #ccc;
            }

            .player-name {
                text-transform: uppercase;
            }

            .player-score {
                font-weight: bold;
            }

            /* HUD */
            .hud {
                margin-bottom: 20px;
                text-align: center;
                width: 100%;
            }

            .hud-score-label {
                color: var(--neon-cyan);
                font-size: 0.8rem;
                margin-bottom: 10px;
            }

            .hud-score {
                color: #fff;
                font-size: 1.5rem;
                text-shadow: 2px 2px 0px var(--neon-pink);
            }

            /* START OVERLAY */
            .start-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.92);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 10;
            }

            .press-start {
                font-size: 1rem;
                color: var(--neon-green);
                animation: blink 0.8s infinite;
                background: none;
                border: 4px solid var(--neon-green);
                padding: 20px;
                cursor: pointer;
                font-family: 'Press Start 2P', cursive;
                text-transform: uppercase;
                box-shadow: 0 0 10px var(--neon-green);
            }

            .press-start:hover {
                background: var(--neon-green);
                color: #000;
            }

            @keyframes blink {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0;
                }
            }

            @media (min-width: 1024px) {
                .arcade-cabinet {
                    flex-direction: row;
                }

                .leaderboard-section {
                    width: 380px;
                    border-left: 4px solid #333;
                    border-top: none;
                }

                .game-section {
                    border-right: none;
                    border-bottom: none;
                }
            }

            /* Mobile Controls */
            .controls {
                margin-top: 20px;
                display: grid;
                grid-template-columns: repeat(3, 50px);
                gap: 15px;
            }

            .d-btn {
                width: 50px;
                height: 50px;
                background: #222;
                border: 2px solid #555;
                color: #fff;
                border-radius: 5px;
                font-size: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 0 #000;
                font-family: inherit;
            }

            .d-btn:active {
                background: #444;
                box-shadow: 0 0 0 #000;
                transform: translateY(4px);
            }

            @media (min-width: 768px) {
                .controls {
                    display: none;
                }
            }
        </style>
    @endpush

    <div class="tetris-page-container">
        <div class="arcade-cabinet">

            <!-- LEFT: GAME -->
            <div class="game-section">
                <div class="crt-overlay"></div>

                <div class="hud">
                    <div class="hud-score-label">SCORE</div>
                    <div class="hud-score" id="score">0</div>
                </div>

                <div style="position: relative;">
                    <canvas id="tetris" width="240" height="400"></canvas>

                    <!-- Start/Game Over Overlay -->
                    <div class="start-overlay" id="overlay">
                        <h2 class="text-white mb-8 text-center" id="overlay-title"
                            style="font-size: 1.5rem; line-height: 2; text-shadow: 4px 4px 0 #000; color: #fff;">
                            <span style="color: var(--neon-pink)">T</span><span
                                style="color: var(--neon-cyan)">E</span><span
                                style="color: var(--neon-yellow)">T</span><span
                                style="color: var(--neon-green)">R</span><span
                                style="color: var(--neon-pink)">I</span><span style="color: var(--neon-cyan)">S</span>
                        </h2>
                        <button class="press-start" id="start-btn">INSERT COIN</button>
                        <p class="text-zinc-500 mt-8 text-sm text-center" id="final-score-display"
                            style="display:none; line-height: 1.5;"></p>
                    </div>
                </div>

                <!-- Mobile Controls -->
                <div class="controls">
                    <button class="d-btn" onclick="if(window.playerMove) window.playerMove(-1)">←</button>
                    <button class="d-btn" onclick="if(window.playerRotate) window.playerRotate()">↻</button>
                    <button class="d-btn" onclick="if(window.playerMove) window.playerMove(1)">→</button>
                    <button class="d-btn" style="grid-column: 2;"
                        onclick="if(window.playerHardDrop) window.playerHardDrop()">↓</button>
                </div>

                <div class="mt-8 text-zinc-600 hidden md:block text-xs text-center" style="line-height: 1.5;">
                    ARROWS TO MOVE<br>
                    UP TO ROTATE<br>
                    DOWN TO DROP
                </div>
            </div>

            <!-- RIGHT: LEADERBOARD -->
            <div class="leaderboard-section">
                <h2 class="retro-title">HIGH SCORES</h2>

                @if($scores->isEmpty())
                    <p class="text-center mt-10 text-zinc-500 text-xs">BE THE FIRST...</p>
                @else
                    <ul class="score-list">
                        @foreach($scores as $index => $score)
                            <li class="score-item">
                                <span class="player-name">
                                    {{ $index + 1 }}.{{ strtoupper(substr($score->first_name, 0, 7)) }}
                                </span>
                                <span class="score-separator"></span>
                                <span class="player-score">{{ number_format($score->score) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-auto text-center pt-8 text-zinc-600 text-xs">
                    <p>CREDITS: 0</p>
                    <p class="mt-2 text-zinc-700">© 1984 TETRIS</p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            // Wait for window load AND a small delay to ensure Vue has finished mounting the DOM
            window.addEventListener('load', () => {
                setTimeout(() => {
                    initTetris();
                }, 500);
            });

            function initTetris() {
                console.log('Tetris: Initializing after window load...');

                const canvas = document.getElementById('tetris');
                if (!canvas) {
                    console.error('Tetris: Canvas not found!');
                    return;
                }

                const context = canvas.getContext('2d');
                const scoreElement = document.getElementById('score');
                const overlay = document.getElementById('overlay');
                const overlayTitle = document.getElementById('overlay-title');
                const startBtn = document.getElementById('start-btn');
                const finalScoreDisplay = document.getElementById('final-score-display');

                context.scale(20, 20);

                // Game State
                let isGameRunning = false;
                let dropCounter = 0;
                let dropInterval = 1000;
                let lastTime = 0;
                let requestID = null;

                // Matrices
                const arena = createMatrix(12, 20);
                const player = { pos: { x: 0, y: 0 }, matrix: null, score: 0 };

                // Colors
                const colors = [
                    null,
                    '#FF0D72', '#0DC2FF', '#0DFF72', '#F538FF', '#FF8E0D', '#FFE138', '#3877FF'
                ];

                function createMatrix(w, h) {
                    const matrix = [];
                    while (h--) { matrix.push(new Array(w).fill(0)); }
                    return matrix;
                }

                function createPiece(type) {
                    if (type === 'I') return [[0, 1, 0, 0], [0, 1, 0, 0], [0, 1, 0, 0], [0, 1, 0, 0]];
                    else if (type === 'L') return [[0, 2, 0], [0, 2, 0], [0, 2, 2]];
                    else if (type === 'J') return [[0, 3, 0], [0, 3, 0], [3, 3, 0]];
                    else if (type === 'O') return [[4, 4], [4, 4]];
                    else if (type === 'Z') return [[5, 5, 0], [0, 5, 5], [0, 0, 0]];
                    else if (type === 'S') return [[0, 6, 6], [6, 6, 0], [0, 0, 0]];
                    else if (type === 'T') return [[0, 7, 0], [7, 7, 7], [0, 0, 0]];
                }

                function draw() {
                    context.fillStyle = '#000';
                    context.fillRect(0, 0, canvas.width, canvas.height);
                    drawMatrix(arena, { x: 0, y: 0 });
                    if (player.matrix) {
                        drawMatrix(player.matrix, player.pos);
                    }
                }

                function drawMatrix(matrix, offset) {
                    matrix.forEach((row, y) => {
                        row.forEach((value, x) => {
                            if (value !== 0) {
                                context.fillStyle = colors[value];
                                context.fillRect(x + offset.x, y + offset.y, 1, 1);

                                // 3D effect / borders
                                context.lineWidth = 0.05;
                                context.strokeStyle = 'rgba(0,0,0,0.5)';
                                context.strokeRect(x + offset.x, y + offset.y, 1, 1);
                            }
                        });
                    });
                }

                function merge(arena, player) {
                    player.matrix.forEach((row, y) => {
                        row.forEach((value, x) => {
                            if (value !== 0) {
                                arena[y + player.pos.y][x + player.pos.x] = value;
                            }
                        });
                    });
                }

                function rotate(matrix, dir) {
                    for (let y = 0; y < matrix.length; ++y) {
                        for (let x = 0; x < y; ++x) {
                            [matrix[x][y], matrix[y][x]] = [matrix[y][x], matrix[x][y]];
                        }
                    }
                    if (dir > 0) matrix.forEach(row => row.reverse());
                    else matrix.reverse();
                }

                function collide(arena, player) {
                    const [m, o] = [player.matrix, player.pos];
                    for (let y = 0; y < m.length; ++y) {
                        for (let x = 0; x < m[y].length; ++x) {
                            if (m[y][x] !== 0 && (arena[y + o.y] && arena[y + o.y][x + o.x]) !== 0) {
                                return true;
                            }
                        }
                    }
                    return false;
                }

                function arenaSweep() {
                    let rowCount = 1;
                    outer: for (let y = arena.length - 1; y > 0; --y) {
                        for (let x = 0; x < arena[y].length; ++x) {
                            if (arena[y][x] === 0) {
                                continue outer;
                            }
                        }
                        const row = arena.splice(y, 1)[0].fill(0);
                        arena.unshift(row);
                        ++y;
                        player.score += rowCount * 10;
                        rowCount *= 2;
                    }
                }

                function playerReset() {
                    const pieces = 'ILJOTSZ';
                    player.matrix = createPiece(pieces[pieces.length * Math.random() | 0]);
                    player.pos.y = 0;
                    player.pos.x = (arena[0].length / 2 | 0) - (player.matrix[0].length / 2 | 0);

                    if (collide(arena, player)) {
                        gameOver();
                    }
                }

                function playerDrop() {
                    player.pos.y++;
                    if (collide(arena, player)) {
                        player.pos.y--;
                        merge(arena, player);
                        playerReset();
                        arenaSweep();
                        updateScore();
                    }
                    dropCounter = 0;
                }

                function playerMove(dir) {
                    if (!isGameRunning) return;
                    player.pos.x += dir;
                    if (collide(arena, player)) { player.pos.x -= dir; }
                }
                window.playerMove = playerMove;

                function playerRotate(dir = 1) {
                    if (!isGameRunning) return;
                    const pos = player.pos.x;
                    let offset = 1;
                    rotate(player.matrix, dir);
                    while (collide(arena, player)) {
                        player.pos.x += offset;
                        offset = -(offset + (offset > 0 ? 1 : -1));
                        if (offset > player.matrix[0].length) {
                            rotate(player.matrix, -dir);
                            player.pos.x = pos;
                            return;
                        }
                    }
                }
                window.playerRotate = playerRotate;

                function playerHardDrop() {
                    if (!isGameRunning) return;
                    while (!collide(arena, player)) { player.pos.y++; }
                    player.pos.y--;
                    merge(arena, player);
                    playerReset();
                    arenaSweep();
                    updateScore();
                    dropCounter = 0;
                }
                window.playerHardDrop = playerHardDrop;

                function update(time = 0) {
                    if (!isGameRunning) return;

                    const deltaTime = time - lastTime;
                    lastTime = time;
                    dropCounter += deltaTime;
                    if (dropCounter > dropInterval) {
                        playerDrop();
                    }
                    draw();
                    requestID = requestAnimationFrame(update);
                }

                function updateScore() {
                    scoreElement.innerText = player.score;
                }

                function gameOver() {
                    console.log('Tetris: Game Over');
                    isGameRunning = false;
                    cancelAnimationFrame(requestID);

                    overlayTitle.innerHTML = '<span style="color:red">GAME OVER</span>';
                    startBtn.innerText = "TRY AGAIN";
                    finalScoreDisplay.style.display = 'block';
                    finalScoreDisplay.innerText = "FINAL SCORE: " + player.score;
                    overlay.style.display = 'flex';

                    // Only save positive scores
                    if (player.score > 0) {
                        saveScore(player.score);
                    }
                }

                function saveScore(score) {
                    finalScoreDisplay.innerText += " (SAVING...)";
                    fetch('{{ route("shop.tetris.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ score: score })
                    }).then(res => {
                        if (res.ok) {
                            finalScoreDisplay.innerText = "FINAL SCORE: " + player.score + " (SAVED!)";
                            // Reloading interrupt UX, better to just save
                            setTimeout(() => window.location.reload(), 2000);
                        }
                    });
                }

                // --- Initialization ---

                function startGame() {
                    console.log('Tetris: Starting Game...');
                    overlay.style.display = 'none';

                    // Reset State
                    arena.forEach(row => row.fill(0));
                    player.score = 0;
                    updateScore();
                    isGameRunning = true;
                    dropCounter = 0;
                    lastTime = performance.now(); // Reset time to avoid jump

                    playerReset();

                    if (requestID) cancelAnimationFrame(requestID);
                    update();
                }

                // Attach to global for fallback if needed, but event listener preferred
                window.startGame = startGame;

                // Ensure button works
                startBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    startGame();
                });

                // Input Handling
                document.addEventListener('keydown', event => {
                    // Prevent scrolling for game keys
                    if ([32, 37, 38, 39, 40].indexOf(event.keyCode) > -1) {
                        event.preventDefault();
                    }

                    // Press Start with Space or Enter
                    if ((event.keyCode === 32 || event.keyCode === 13) && !isGameRunning) {
                        startGame();
                        return;
                    }

                    if (!isGameRunning) return;

                    if (event.keyCode === 37) playerMove(-1);
                    else if (event.keyCode === 39) playerMove(1);
                    else if (event.keyCode === 40) playerHardDrop();
                    else if (event.keyCode === 38) playerRotate(1);
                });

                // Initial Draw (Empty)
                context.fillStyle = '#000';
                context.fillRect(0, 0, canvas.width, canvas.height);
                console.log('Tetris: Ready');
            }
        </script>
    @endpush
</x-shop::layouts>