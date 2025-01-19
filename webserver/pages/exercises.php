<?php
// pages/exercises.php
// Displays all tasks from exercises.txt (the JSON file) in unsorted order

$exercisesFile = __DIR__ . '/../exercises.txt';
$exercises = [];

// Load tasks from JSON
if (file_exists($exercisesFile)) {
    $decoded = json_decode(file_get_contents($exercisesFile), true);
    if (is_array($decoded)) {
        $exercises = $decoded;
    }
}
?>
<h1>Tasks</h1>

<?php if (empty($exercises)): ?>
    <p>No tasks available.</p>
<?php else: ?>
    <?php foreach ($exercises as $index => $task): ?>
        <?php 
            // Safely extract values (title, desc, deadline)
            $name = htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8');
            $desc = htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8');
            $dead = htmlspecialchars($task['deadline'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="task">
            <strong><?= $name ?></strong><br>
            <?= $desc ?><br>
            Deadline: <?= $dead ?>
            <a href="?page=exercises&delete=<?= $index ?>" class="delete-button">[X]</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
