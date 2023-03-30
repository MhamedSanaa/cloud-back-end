<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


$connect = mysqli_connect("db", "clouduser", "cloudpwd");

// Check connection
if (!$connect) {
  die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE clouddb";
mysqli_query($connect, $sql);

$connection = mysqli_connect("db", "clouduser", "cloudpwd","clouddb");

// Check connection
if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

// Create table if it does not exist
$sql = "CREATE TABLE IF NOT EXISTS person (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(30) NOT NULL,
  lastname VARCHAR(30) NOT NULL,
  password VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL
)";

mysqli_query($connection, $sql);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate the data
    if (isset($data['firstname']) && isset($data['lastname']) && isset($data['email']) && isset($data['password'])) {
        // Process the data and send a success response
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $email = $data['email'];
        $password = $data['password'];
        
        // Connect to the MySQL database
        $conn = mysqli_connect("db", "clouduser", "cloudpwd", "clouddb");

        // Insert the data into the database
        $sql = "INSERT INTO person (firstname, lastname, password, email) VALUES ('$firstname', '$lastname', '$password', '$email')";
        if (mysqli_query($conn, $sql)) {
            $response = array("success" => true, "message" => "Data inserted successfully");
        } else {
            $response = array("success" => false, "error" => "Error inserting data: " . mysqli_error($conn));
        }

        // Close the database connection
        mysqli_close($conn);

        echo json_encode($response);
    } else {
        // If the data is invalid, send an error response
        http_response_code(400);
        echo json_encode(array("error" => "Invalid data"));
    }
} else {
    // If the request method is not POST, return an error message
    http_response_code(405);
    echo json_encode(array("error" => "Method Not Allowed"));
}
?>