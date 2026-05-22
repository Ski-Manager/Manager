<div class="w-full">
<?php

$lang_b = $this->lang->line('building');

echo '<h2 class="h2">' . $lang_b['sqz_title'] . '</h2>';
echo '<p>'  . $lang_b['sqz_intro'] . '</p>';

// ── Secret-code gate ────────────────────────────────────────────────────────
if (!$unlocked):
    $sqz_error = $this->session->flashdata('sqz_error');
?>

<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12 md:col-span-6 lg:col-span-4">

    <?php if ($sqz_error): ?>
        <?php echo $lang_b['sqz_secret_error']; ?>
    <?php endif; ?>

    <div class="card bg-base-100 shadow-sm"><div class="card-body">
        <h4 class="h4"><?php echo $lang_b['sqz_secret_title']; ?></h4>
        <p><?php echo $lang_b['sqz_secret_desc']; ?></p>

        <?php
        $attr = ['id' => 'sqzUnlockForm'];
        echo form_open('ski_quiz_controller/unlock', $attr);
        ?>
        <div class="mb-3">
            <label for="secret_code" class="label">
                <?php echo $lang_b['sqz_secret_label']; ?>
            </label>
            <input type="password" class="input w-full" id="secret_code" name="secret_code"
                   placeholder="<?php echo htmlspecialchars($lang_b['sqz_secret_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                   autocomplete="off" required>
        </div>
        <button type="submit" class="btn btn-primary">
            <?php echo $lang_b['sqz_secret_submit']; ?>
        </button>
        <?php echo form_close(); ?>
    </div>

</div>
</div>

<?php else: // ── Quiz ─────────────────────────────────────────────────────────
?>

<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

    <div id="sqz_app">

        <!-- Start screen -->
        <div id="sqz_start_screen">
            <div class="grid grid-cols-12 gap-3 mb-3">
                <div class="col-span-12 md:col-span-8">
                    <div class="alert alert-info">
                        <?php echo $lang_b['sqz_instructions']; ?>
                    </div>
                </div>
            </div>
            <button id="sqz_btn_start" class="btn btn-success btn-lg">
                🎿 <?php echo $lang_b['sqz_btn_start']; ?>
            </button>
        </div>

        <!-- Question screen -->
        <div id="sqz_question_screen" style="display:none;">
            <div class="grid grid-cols-12 gap-3 mb-3">
                <div class="col-span-12 md:col-span-8">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold" id="sqz_progress_label"></span>
                        <span class="badge badge-primary text-base" id="sqz_score_badge">
                            <?php echo $lang_b['sqz_score']; ?>: <span id="sqz_score_val">0</span>
                        </span>
                    </div>
                    <progress class="progress progress-success w-full mb-3" style="height:8px;" id="sqz_progress_bar" value="0" max="100"></progress>
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <p class="text-xl font-semibold mb-4" id="sqz_question_text"></p>
                            <div id="sqz_options" class="grid gap-2">
                                <!-- Options rendered by JS -->
                            </div>
                        </div>
                    </div>
                    <div id="sqz_feedback" style="display:none;" class="mb-3"></div>
                    <button id="sqz_btn_next" class="btn btn-primary" style="display:none;">
                        <?php echo $lang_b['sqz_btn_next']; ?> ›
                    </button>
                </div>
            </div>
        </div>

        <!-- Results screen -->
        <div id="sqz_results_screen" style="display:none;">
            <div class="grid grid-cols-12 gap-3">
                <div class="col-span-12 md:col-span-6">
                    <div class="card shadow-sm text-center">
                        <div class="card-body py-4">
                            <div style="font-size:3rem;" id="sqz_result_emoji">🏆</div>
                            <h3 class="h3 mt-2"><?php echo $lang_b['sqz_result_title']; ?></h3>
                            <p class="lead">
                                <?php echo $lang_b['sqz_result_score_label']; ?>
                                <strong id="sqz_final_score"></strong>
                            </p>
                            <p class="text-base-content/60" id="sqz_result_message"></p>
                            <button id="sqz_btn_restart" class="btn btn-success mt-2">
                                🔄 <?php echo $lang_b['sqz_btn_restart']; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /#sqz_app -->

</div>

<script>
(function () {
    'use strict';

    // ── Localised strings ───────────────────────────────────────────────────
    var L = {
        question    : <?php echo json_encode($lang_b['sqz_question']); ?>,
        correct     : <?php echo json_encode($lang_b['sqz_feedback_correct']); ?>,
        incorrect   : <?php echo json_encode($lang_b['sqz_feedback_incorrect']); ?>,
        score       : <?php echo json_encode($lang_b['sqz_score']); ?>,
        resultGold  : <?php echo json_encode($lang_b['sqz_result_gold']); ?>,
        resultSilver: <?php echo json_encode($lang_b['sqz_result_silver']); ?>,
        resultBronze: <?php echo json_encode($lang_b['sqz_result_bronze']); ?>,
        resultTryAgain: <?php echo json_encode($lang_b['sqz_result_try_again']); ?>
    };

    // ── Question bank ───────────────────────────────────────────────────────
    var ALL_QUESTIONS = [
        {
            q: "What colour is used to mark the easiest ski runs?",
            opts: ["Green", "Blue", "Red", "Black"],
            a: 0
        },
        {
            q: "Which country hosts the famous Hahnenkamm downhill race?",
            opts: ["Switzerland", "France", "Austria", "Italy"],
            a: 2
        },
        {
            q: "What does 'après-ski' mean?",
            opts: ["Morning warm-up exercises", "Activities and socialising after skiing", "Snow grooming at night", "Ski rental service"],
            a: 1
        },
        {
            q: "What is a 'mogul' on a ski slope?",
            opts: ["A type of ski lift", "A bump or mound of snow formed by skiers", "A ski safety barrier", "A weather forecast tool"],
            a: 1
        },
        {
            q: "Which alpine skiing discipline involves racing between two close-set gates?",
            opts: ["Downhill", "Super-G", "Slalom", "Freestyle"],
            a: 2
        },
        {
            q: "What is the main purpose of snowmaking machines at a ski resort?",
            opts: ["To cool the lodge", "To create artificial snow when natural snow is insufficient", "To water the summer grass", "To blast ice off lifts"],
            a: 1
        },
        {
            q: "What safety feature is worn by skiers to reduce head injury risk?",
            opts: ["Goggles", "Helmet", "Gloves", "Knee pads"],
            a: 1
        },
        {
            q: "Which country is home to the world-famous resort of Chamonix?",
            opts: ["Switzerland", "Austria", "France", "Italy"],
            a: 2
        },
        {
            q: "What does a ski resort's 'vertical drop' measure?",
            opts: ["The length of the longest run", "The height difference between the top and bottom of the ski area", "The average snowfall depth", "The steepest section gradient"],
            a: 1
        },
        {
            q: "What is a 'gondola' in the context of a ski resort?",
            opts: ["A type of skis", "An enclosed cabin lift", "A snow grooming machine", "A ski racing course"],
            a: 1
        },
        {
            q: "Which skiing style involves performing jumps and tricks in a half-pipe?",
            opts: ["Alpine", "Nordic", "Freestyle", "Telemark"],
            a: 2
        },
        {
            q: "What is the term for the person who maintains and grooms ski runs overnight?",
            opts: ["Ski Patrol", "Pisteur", "Piste Basher", "Snow Groomer"],
            a: 3
        },
        {
            q: "At what temperature (°C) can snowmaking guns typically begin producing snow?",
            opts: ["0°C", "-2°C", "-5°C", "-10°C"],
            a: 1
        },
        {
            q: "What does a 'black diamond' run classification indicate?",
            opts: ["A beginner-friendly run", "An intermediate run", "An expert or very difficult run", "An off-piste area"],
            a: 2
        },
        {
            q: "Which of the following is NOT a type of ski lift?",
            opts: ["Chairlift", "Gondola", "Funicular", "Skidoo"],
            a: 3
        }
    ];

    // Number of questions to ask per game
    var QUESTIONS_PER_GAME = 10;

    // ── State ───────────────────────────────────────────────────────────────
    var questions    = [];
    var currentIndex = 0;
    var score        = 0;
    var answered     = false;

    // ── DOM references ──────────────────────────────────────────────────────
    var startScreen    = document.getElementById('sqz_start_screen');
    var questionScreen = document.getElementById('sqz_question_screen');
    var resultsScreen  = document.getElementById('sqz_results_screen');
    var progressLabel  = document.getElementById('sqz_progress_label');
    var progressBar    = document.getElementById('sqz_progress_bar');
    var scoreVal       = document.getElementById('sqz_score_val');
    var questionText   = document.getElementById('sqz_question_text');
    var optionsBox     = document.getElementById('sqz_options');
    var feedbackBox    = document.getElementById('sqz_feedback');
    var btnNext        = document.getElementById('sqz_btn_next');
    var finalScore     = document.getElementById('sqz_final_score');
    var resultMessage  = document.getElementById('sqz_result_message');
    var resultEmoji    = document.getElementById('sqz_result_emoji');

    // ── Helpers ─────────────────────────────────────────────────────────────
    function shuffle(arr) {
        var a = arr.slice();
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = a[i]; a[i] = a[j]; a[j] = tmp;
        }
        return a;
    }

    function startQuiz() {
        questions    = shuffle(ALL_QUESTIONS).slice(0, QUESTIONS_PER_GAME);
        currentIndex = 0;
        score        = 0;
        scoreVal.textContent = '0';
        startScreen.style.display    = 'none';
        resultsScreen.style.display  = 'none';
        questionScreen.style.display = 'block';
        showQuestion();
    }

    function showQuestion() {
        answered = false;
        feedbackBox.style.display = 'none';
        btnNext.style.display     = 'none';

        var q   = questions[currentIndex];
        var num = currentIndex + 1;
        var tot = questions.length;

        progressLabel.textContent = L.question + ' ' + num + ' / ' + tot;
        progressBar.value         = Math.round((num / tot) * 100);
        questionText.textContent  = q.q;

        optionsBox.innerHTML = '';
        q.opts.forEach(function (opt, idx) {
            var btn = document.createElement('button');
            btn.className   = 'btn btn-outline text-left';
            btn.textContent = opt;
            btn.dataset.idx = idx;
            btn.addEventListener('click', function () { selectAnswer(idx, q.a, btn); });
            optionsBox.appendChild(btn);
        });
    }

    function selectAnswer(selected, correct, clickedBtn) {
        if (answered) return;
        answered = true;

        // Disable all option buttons
        var btns = optionsBox.querySelectorAll('button');
        btns.forEach(function (b) { b.disabled = true; });

        if (selected === correct) {
            score++;
            scoreVal.textContent = score;
            clickedBtn.classList.replace('btn-outline', 'btn-success');
            feedbackBox.className   = 'alert alert-success mb-3';
            feedbackBox.textContent = L.correct;
        } else {
            clickedBtn.classList.replace('btn-outline', 'btn-error');
            btns[correct].classList.replace('btn-outline', 'btn-success');
            feedbackBox.className   = 'alert alert-error mb-3';
            feedbackBox.textContent = L.incorrect;
        }

        feedbackBox.style.display = 'block';
        btnNext.style.display     = 'inline-block';
    }

    function nextQuestion() {
        currentIndex++;
        if (currentIndex < questions.length) {
            showQuestion();
        } else {
            showResults();
        }
    }

    function showResults() {
        questionScreen.style.display = 'none';
        resultsScreen.style.display  = 'block';

        var pct = score / questions.length;
        finalScore.textContent = score + ' / ' + questions.length;

        if (pct >= 0.8) {
            resultEmoji.textContent   = '🏆';
            resultMessage.textContent = L.resultGold;
        } else if (pct >= 0.5) {
            resultEmoji.textContent   = '🥈';
            resultMessage.textContent = L.resultSilver;
        } else if (pct >= 0.3) {
            resultEmoji.textContent   = '🥉';
            resultMessage.textContent = L.resultBronze;
        } else {
            resultEmoji.textContent   = '❄️';
            resultMessage.textContent = L.resultTryAgain;
        }
    }

    // ── Event listeners ─────────────────────────────────────────────────────
    document.getElementById('sqz_btn_start').addEventListener('click', startQuiz);
    btnNext.addEventListener('click', nextQuestion);
    document.getElementById('sqz_btn_restart').addEventListener('click', function () {
        resultsScreen.style.display = 'none';
        startScreen.style.display   = 'block';
    });

}());
</script>

<?php endif; // end unlocked ?>

</div>
