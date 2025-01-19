<?php
// pages/exercise.php
?>
<h1>Add a New Task</h1>

<div class="form-container">
    <form action="?page=exercise" method="POST">
        <label for="taskName">Task Title</label>
        <input type="text" id="taskName" name="name" required>

        <label for="taskDesc">Description</label>
        <input type="text" id="taskDesc" name="description" required>

        <label for="taskDate">Deadline (YYYY-MM-DD)</label>
        <input type="date" id="taskDate" name="deadline" required>

        <button type="submit">Add Task</button>
    </form>
</div>
