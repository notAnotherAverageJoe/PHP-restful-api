<?php
require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// (GET)
if ($path === '/tasks' && $method === 'GET') {
    header("Content-Type: application/json");
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY id");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// (POST)
if ($path === '/tasks' && $method === 'POST') {
    header("Content-Type: application/json");
    $input = json_decode(file_get_contents('php://input'), true);
    $title = $input['title'] ?? '';

    if ($title) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
        $stmt->execute(['title' => $title]);
        echo json_encode(["message" => "Task Created!"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Title is required"]);
    }
    exit;
}

// (PUT)
if (strpos($path, '/tasks') === 0 && $method === 'PUT') {
    header("Content-Type: application/json");
    parse_str($_SERVER['QUERY_STRING'], $params);
    $id = $params['id'] ?? null;

    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }

    $title = $input['title'] ?? '';
    $done = isset($input['done']) ? (int)$input['done'] : 0;

    if ($id && $title !== '') {
        $stmt = $pdo->prepare("UPDATE tasks SET title = :title, done = :done WHERE id = :id");
        $stmt->execute(['title' => $title, 'done' => $done, 'id' => $id]);
        echo json_encode(['message' => 'Task Updated!']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID and title required']);
    }
    exit;
}


if (strpos($path, '/tasks') === 0 && $method === 'DELETE') {
    header("Content-Type: application/json");
    parse_str($_SERVER['QUERY_STRING'], $params);
    $id = $params['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['message' => 'Task deleted']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID required']);
    }
    exit;
}

// ant unknown routes
if ($path !== '/') {
    http_response_code(404);
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Invalid route or method']);
    exit;
}
?>

<!-- main page-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP RESTful API</title>
</head>

<body>
    <h1>Welcome to the Task API</h1>
    <p>This API supports basic task CRUD operations.</p>
    <h2>Available Routes</h2>
    <ul>
        <li><strong>GET /tasks</strong> — List all tasks</li>
        <li><strong>POST /tasks</strong> — Create a new task</li>
        <li><strong>PUT /tasks?id=1</strong> — Update task with ID 1</li>
        <li><strong>DELETE /tasks?id=1</strong> — Delete task with ID 1</li>
    </ul>
</body>

</html>