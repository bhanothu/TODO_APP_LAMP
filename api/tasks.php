<?php
session_start(); // Start session to get user data
include('config.php'); // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Handle GET request to fetch tasks
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM todos WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    // Set the header for JSON response
    header('Content-Type: application/json');
    echo json_encode($tasks); // Return the tasks as JSON
    exit;
}

// Handle POST request to add a task
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the raw POST data and decode it as JSON
    $data = json_decode(file_get_contents('php://input'), true);
    $task_content = $data['content'];
    $user_id = $_SESSION['user_id'];

    // Insert task into the database
    $sql = "INSERT INTO todos (content, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $task_content, $user_id);
    if ($stmt->execute()) {
        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return error response if the insert fails
        echo json_encode(['success' => false, 'error' => 'Failed to add task']);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'PUT' && isset($_GET['action']) && $_GET['action'] == 'mark_done') {
    $task_id = $_GET['id'];
    $sql = "UPDATE todos SET done = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $task_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to mark task as done']);
    }
    exit;
}

// Handle DELETE request to delete a task
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['action']) && $_GET['action'] == 'delete') {
    $task_id = $_GET['id'];
    $sql = "DELETE FROM todos WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $task_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete task']);
    }
    exit;
}
?>
