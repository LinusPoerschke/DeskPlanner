<?php
// pages/week.php
// Displays only tasks that fall into the current calendar week (Monday-Sunday)

$exercisesFile = __DIR__ . '/../exercises.txt';
$exercises = [];

// Load tasks from JSON
if (file_exists($exercisesFile)) {
    $decoded = json_decode(file_get_contents($exercisesFile), true);
    if (is_array($decoded)) {
        $exercises = $decoded;
    }
}

// Step 1: Calculate current Monday
$today = new DateTime();
$dayOfWeek = $today->format('N'); // 1=Monday, 2=Tuesday, ... 7=Sunday
$monday = clone $today;
$monday->modify('-' . ($dayOfWeek - 1) . ' days'); // go back to Monday

// Step 2: Calculate Sunday (Monday + 6 days)
$sunday = clone $monday;
$sunday->modify('+6 days');

// Prepare array to hold tasks for each weekday
$weekDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

// Create an array like ["Monday" => [], "Tuesday" => [], ...]
$tasksByDay = [];
foreach ($weekDays as $wd) {
    $tasksByDay[$wd] = [];
}

// Step 3: Filter tasks that fall within [Monday..Sunday]
foreach ($exercises as $index => $task) {
    if (!empty($task['deadline'])) {
        $deadlineDate = DateTime::createFromFormat('Y-m-d', $task['deadline']);
        if ($deadlineDate) {
            // Check if the date is between Monday and Sunday
            if ($deadlineDate >= $monday && $deadlineDate <= $sunday) {
                // Which weekday?
                $weekdayName = $deadlineDate->format('l'); // "Monday", "Tuesday", ...
                if (isset($tasksByDay[$weekdayName])) {
                    // Add this task to that weekday
                    $tasksByDay[$weekdayName][] = [
                        'index' => $index,
                        'name' => $task['name'],
                        'description' => $task['description'],
                        'deadline' => $task['deadline']
                    ];
                }
            }
        }
    }
}

// Step 4: Display them in a grid
?>
<h1>Current Week</h1>

<div class="weekdays-container">
    <?php
    // We'll loop from Monday to Sunday
    $currentDay = clone $monday; // start from Monday

    foreach ($weekDays as $wdName) {
        // Format e.g.  "07.04.2025"
        $formattedDate = $currentDay->format('d.m.Y');
        ?>
        <div class="weekday-container">
            <div class="weekday"><?= $wdName ?></div>
            <div class="date"><?= $formattedDate ?></div>

            <?php if (empty($tasksByDay[$wdName])): ?>
                <p>No tasks</p>
            <?php else: ?>
                <?php foreach ($tasksByDay[$wdName] as $taskInfo): ?>
                    <?php 
                        $tName = htmlspecialchars($taskInfo['name'], ENT_QUOTES, 'UTF-8');
                        $tDesc = htmlspecialchars($taskInfo['description'], ENT_QUOTES, 'UTF-8');
                        $tDead = htmlspecialchars($taskInfo['deadline'], ENT_QUOTES, 'UTF-8');
                        $tIdx  = $taskInfo['index'];
                    ?>
                    <div class="task">
                        <strong><?= $tName ?></strong><br>
                        <?= $tDesc ?><br>
                        Deadline: <?= $tDead ?>
                        <a href="?page=week&delete=<?= $tIdx ?>" class="delete-button">[X]</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
        // move to next day
        $currentDay->modify('+1 day');
    }
    ?>
</div>
