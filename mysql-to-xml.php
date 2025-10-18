<?php
// Database connection
$conn = new mysqli('localhost', 'root', 'shakil', 'shopping_cart');

// Fetch data from users table
$result = $conn->query("SELECT * FROM students");

// Create XML structure
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><students></students>');

while ($row = $result->fetch_assoc()) {
    $student = $xml->addChild('student');
    $student->addChild('id', $row['id']);
    $student->addChild('name', htmlspecialchars($row['name']));
    $student->addChild('email', htmlspecialchars($row['email']));
    $student->addChild('age', $row['age']);
    $student->addChild('course', htmlspecialchars($row['course']));
}

// Save to file
$xml->asXML('students_output.xml');

echo "Data exported from MySQL to XML successfully!";
$conn->close();
?>
