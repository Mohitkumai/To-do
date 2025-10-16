<?php
// Include the database configuration
require_once 'db_config.php';
// Set the content type to JSON for all responses
header('Content-Type: application/json');
// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];
// Handle the request based on the method
switch ($method) {
    case 'GET':
        handle_get_request($conn);
        break;
    case 'POST':
        handle_post_request($conn);
        break;
    default:
        // Handle unsupported methods
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
// Close the database connection
$conn->close();
/**
 * Handles GET requests to fetch all tasks.
 *
 * @param mysqli $conn The database connection object.
 */
function handle_get_request($conn) {
    $sql = "SELECT id, task, is_completed FROM tasks ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if (!$result) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Error fetching tasks: ' . $conn->error]);
        return;
    }
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        // Convert is_completed to a boolean for JSON consistency
        $row['is_completed'] = (bool) $row['is_completed'];
        $tasks[] = $row;
    }
    
    echo json_encode($tasks);
}
/**
 * Handles POST requests to add, update, or delete tasks.
 *
 * @param mysqli $conn The database connection object.
 */
function handle_post_request($conn) {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if data and action are present
    if (!isset($data['action'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Action not specified']);
        return;
    }
    $action = $data['action'];

    switch ($action) {
        case 'add':
            add_task($conn, $data);
            break;
        case 'update':
            update_task_status($conn, $data);
            break;
        case 'delete':
            delete_task($conn, $data);
            break;
        default:
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Invalid action']);
            break;
    }
}
/**
 * Adds a new task to the database.
 */
function add_task($conn, $data) {
    if (empty($data['task'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Task content cannot be empty']);
        return;
    }
    $task = $data['task'];
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO tasks (task) VALUES (?)");
    $stmt->bind_param("s", $task);

    if ($stmt->execute()) {
        $new_task_id = $conn->insert_id;
        echo json_encode(['message' => 'Task added successfully', 'id' => $new_task_id]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error adding task: ' . $stmt->error]);
    }
    $stmt->close();
}
/**
 * Updates the completion status of a task.
 */
function update_task_status($conn, $data) {
    if (!isset($data['id']) || !isset($data['is_completed'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Task ID and completion status are required']);
        return;
    }
    $id = (int)$data['id'];
    $is_completed = (int)(bool)$data['is_completed']; // Cast to boolean then to int (0 or 1)

    $stmt = $conn->prepare("UPDATE tasks SET is_completed = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_completed, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Task updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error updating task: ' . $stmt->error]);
    }
    $stmt->close();
}
/**
 * Deletes a task from the database.
 */
function delete_task($conn, $data) {
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Task ID is required']);
        return;
    }
    $id = (int)$data['id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Task deleted successfully']);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['message' => 'Task not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error deleting task: ' . $stmt->error]);
    }
    $stmt->close();
}
?>