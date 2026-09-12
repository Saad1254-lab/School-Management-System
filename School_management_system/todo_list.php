<?php
include 'db.php';
include 'navbar.php';




// Add a task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task = $conn->real_escape_string($_POST['task']);
    $query = "INSERT INTO tasks (task) VALUES ('$task')";
    $conn->query($query);
    header("Location: todo_list.php");
    exit;
}

// Mark task as completed
if (isset($_GET['complete_task'])) {
    $id = (int) $_GET['complete_task'];
    $query = "UPDATE tasks SET status='Completed' WHERE id=$id";
    $conn->query($query);
    header("Location: todo_list.php");
    exit;
}

// Delete a task
if (isset($_GET['delete_task'])) {
    $id = (int) $_GET['delete_task'];
    $query = "DELETE FROM tasks WHERE id=$id";
    $conn->query($query);
   header("Location: todo_list.php");
    exit;
}

// Fetch tasks
$query = "SELECT * FROM tasks ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin-top: 20px;
            font-size: 2.5em;
            color: #444;
        }
        form {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 300px;
        }
        button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .task-list {
            width: 60%;
            margin: 0 auto;
        }
        .task {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .task span {
            font-size: 18px;
        }
        .completed {
            text-decoration: line-through;
            color: gray;
        }
        .actions button {
            background-color: #d9534f;
            margin-left: 10px;
        }
        .actions button.complete {
            background-color: #0275d8;
        }
        .actions button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
 <h1 style="text-align: center;">To-Do List</h1>

   <div style="display: flex; justify-content: center;">
    <form method="POST" action="" style="text-align: center;">
        <input type="text" name="task" placeholder="Enter a new task" required style="margin-bottom: 10px; padding: 5px; width: 200px;">
        <button type="submit" name="add_task" style="padding: 5px 10px;">Add Task</button>
    </form>
</div>


    <div class="task-list">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="task <?= $row['status'] === 'Completed' ? 'completed' : '' ?>">
                <span><?= htmlspecialchars($row['task']) ?></span>
                <div class="actions">
                    <?php if ($row['status'] !== 'Completed'): ?>
                        <a href="?complete_task=<?= $row['id'] ?>">
                            <button class="complete">Mark as Done</button>
                        </a>
                    <?php endif; ?>
                    <a href="?delete_task=<?= $row['id'] ?>">
                        <button>Delete</button>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
