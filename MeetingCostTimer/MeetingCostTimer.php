<?php
session_start();
$xmlFile = __DIR__ . '/meetings.xml';

if (isset($_POST['add_participant'])) {
    $_SESSION['participants'][] = [
        'name' => $_POST['name'],
        'salary' => $_POST['salary']
    ];
}
if (isset($_POST['clear_participants'])) $_SESSION['participants'] = [];



if (isset($_POST['save_meeting'])) {
    $xml = simplexml_load_file($xmlFile);
    $meeting = $xml->addChild('meeting');
    $meeting->addChild('date', date('Y-m-d H:i:s'));
    $meeting->addChild('duration', $_POST['duration']);
    $meeting->addChild('money', $_POST['total_cost']);
    $xml->asXML($xmlFile);
}

$totalSalaryPerSec = array_sum(array_column($_SESSION['participants'] ?? [], 'salary')) / 3600;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Часы для совещаний</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .box { border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        button { margin: 5px; padding: 8px 15px; cursor: pointer; }
        .timer-display { font-size: 24px; font-weight: bold; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .participant { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="box">
        <h2>1. Участники совещания</h2>
        <form method="post">
            <input type="text" name="name" placeholder="Имя участника" required>
            <input type="number" step="0.01" name="salary" placeholder="Зарплата USD/час" required>
            <button name="add_participant">Добавить</button>
            <button name="clear_participants">Очистить</button>
        </form>
        <ol>
            <?php foreach (($_SESSION['participants'] ?? []) as $p): ?>
                <li><?= htmlspecialchars($p['name']) ?>: <?= $p['salary'] ?> USD/час</li>
            <?php endforeach; ?>
        </ol>
        <?php if (!empty($_SESSION['participants'])): ?>
            <div style="background:#3498db; color:white; padding:10px;">
                Суммарно: <?= array_sum(array_column($_SESSION['participants'], 'salary')) ?> USD/час
            </div>
        <?php endif; ?>
    </div>

    <div class="box">
        <h2>2. Таймер совещания</h2>
        <div id="timer" class="timer-display">0 сек. (0 USD)</div>
        <button onclick="startTimer()" id="startBtn" <?= empty($_SESSION['participants']) ? 'disabled' : '' ?>>Старт</button>
        <button onclick="stopTimer()" id="stopBtn" disabled>Стоп</button>
    </div>

    <form id="saveForm" method="post" style="display:none;">
        <input type="hidden" name="total_cost" id="res_cost">
        <input type="hidden" name="duration" id="res_duration">
        <button name="save_meeting">Сохранить в XML</button>
    </form>

    <div class="box">
    <h2>3. История совещаний</h2>
    <table border="1">
        <tr><th>Дата/время</th><th>Длительность (сек)</th><th>Потрачено</th></tr>
        <?php
        if (file_exists($xmlFile)) {
            $history = simplexml_load_file($xmlFile);
            if ($history && isset($history->meeting)) {
                $meetings = [];
                foreach ($history->meeting as $m) {
                    $meetings[] = $m;
                }
                $meetings = array_reverse($meetings);
                
                foreach ($meetings as $m) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($m->date) . "</td>";
                    echo "<td>" . htmlspecialchars($m->duration) . "</td>";
                    echo "<td>" . htmlspecialchars($m->money) . " USD</td>";
                    echo "</tr>";
                }
            }
        }
        ?>
    </table>
</div>

    <script>
        let startTime, timerInterval;
        let isRunning = false;
        const ratePerSec = <?= $totalSalaryPerSec ?>;

        function startTimer() {
            if (isRunning) return;
            startTime = Date.now();
            isRunning = true;
            document.getElementById('startBtn').disabled = true;
            document.getElementById('stopBtn').disabled = false;
            
            timerInterval = setInterval(() => {
                let seconds = (Date.now() - startTime) / 1000;
                let cost = seconds * ratePerSec;
                document.getElementById('timer').innerText = Math.round(seconds) + " сек. (" + cost.toFixed(2) + " USD)";
                document.getElementById('res_cost').value = cost.toFixed(2);
                document.getElementById('res_duration').value = Math.round(seconds);
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timerInterval);
            isRunning = false;
            document.getElementById('saveForm').style.display = 'block';
            document.getElementById('startBtn').disabled = false;
            document.getElementById('stopBtn').disabled = true;
        }
    </script>
</body>
</html>
