<?php
// Database connection
$conn = new mysqli('localhost', 'root', 'shakil', 'shopping_cart');

// Create students table
$conn->query("CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    age INT,
    course VARCHAR(100)
)");

// Read XML file
$xml = simplexml_load_file('students.xml');

// Insert data
foreach ($xml->student as $student) {
    $name = $conn->real_escape_string($student->name);
    $email = $conn->real_escape_string($student->email);
    $age = (int)$student->age;
    $course = $conn->real_escape_string($student->course);
    
    $conn->query("INSERT INTO students (name, email, age, course) VALUES ('$name', '$email', $age, '$course')");
}

echo "Data imported from XML to MySQL successfully!";
$conn->close();
?>
