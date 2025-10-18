<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

// Database connection
$conn = new mysqli('localhost', 'root', 'shakil', 'shopping_cart');

switch ($method) {
    case 'GET':
        // Get all students
        $result = $conn->query("SELECT * FROM students");
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode($students);
        break;
        
    case 'POST':
        // Create new student
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $conn->real_escape_string($data['name']);
        $email = $conn->real_escape_string($data['email']);
        $age = (int)$data['age'];
        $course = $conn->real_escape_string($data['course']);
        
        $result = $conn->query("INSERT INTO students (name, email, age, course) VALUES ('$name', '$email', $age, '$course')");
        
        if ($result) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;
        
    case 'PUT':
        // Update student
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)$data['id'];
        $name = $conn->real_escape_string($data['name']);
        $email = $conn->real_escape_string($data['email']);
        $age = (int)$data['age'];
        $course = $conn->real_escape_string($data['course']);
        
        $result = $conn->query("UPDATE students SET name='$name', email='$email', age=$age, course='$course' WHERE id=$id");
        
        echo json_encode(['success' => $result]);
        break;
        
    case 'DELETE':
        // Delete student
        $id = (int)$_GET['id'];
        $result = $conn->query("DELETE FROM students WHERE id=$id");
        
        echo json_encode(['success' => $result]);
        break;
}

$conn->close();
?>
