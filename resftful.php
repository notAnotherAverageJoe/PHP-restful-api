<?php
require_once "db.php";

header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Route: /tasks
if ($_SERVER['REQUEST_URI'] === '/tasks' && $method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY id");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Route: /tasks (POST)
if ($_SERVER['REQUEST_URI'] === '/tasks' && $method === 'POST') {
    $title = $input['title'] ?? '';
    if ($title) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
        $stmt->execute(['title' => $title]);
        echo json_encode(['message' => 'Task created']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
    }
    exit;
}

// Route: /tasks?id=1 (PUT)
if (strpos($_SERVER['REQUEST_URI'], '/tasks') === 0 && $method === 'PUT') {
    parse_str($_SERVER['QUERY_STRING'], $params);
    $id = $params['id'] ?? null;
    $title = $input['title'] ?? '';
    $done = $input['done'] ?? false;

    if ($id && $title !== '') {
        $stmt = $pdo->prepare("UPDATE tasks SET title = :title, done = :done WHERE id = :id");
        $stmt->execute(['title' => $title, 'done' => $done, 'id' => $id]);
        echo json_encode(['message' => 'Task updated']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID and title required']);
    }
    exit;
}

// Route: /tasks?id=1 (DELETE)
if (strpos($_SERVER['REQUEST_URI'], '/tasks') === 0 && $method === 'DELETE') {
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

http_response_code(404);
echo json_encode(['error' => 'Invalid route or method']);
