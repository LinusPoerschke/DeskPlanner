<?php
// pages/deadlines.php
// Displays tasks sorted by their 'deadline' field

$exercises = readTasks();

// Sort tasks by date
usort($exercises, function ($a, $b) {
    $timeA = strtotime($a['deadline'] ?? '1970-01-01');
    $timeB = strtotime($b['deadline'] ?? '1970-01-01');
    return $timeA - $timeB;
});
?>
<h1>Deadlines</h1>

<?php if (empty($exercises)): ?>
    <p>No tasks available.</p>
<?php else: ?>
    <?php foreach ($exercises as $index => $task): ?>
        <?php 
            $name = htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8');
            $desc = htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8');
            $dead = htmlspecialchars($task['deadline'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="task">
            <strong><?= $name ?></strong><br>
            <?= $desc ?><br>
            Deadline: <?= $dead ?>
            <a href="?page=deadlines&delete=<?= $index ?>" class="delete-button">[X]</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
