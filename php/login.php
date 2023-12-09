<?php

$serverName = "localhost";
$dbUsername = "root";
$dbPassword = "1234";
$dbName = "guvi";

$conn = new mysqli($serverName, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json_data = file_get_contents('php://input');
    $requestData = json_decode($json_data, true);

    $inputEmail = $requestData['email'];
    $inputPassword = $requestData['password'];

    $stmt = $conn->prepare("SELECT email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $inputEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($inputPassword, $user['password'])) {
            $token = bin2hex(random_bytes(16));

            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->setex('token_' . $token, 3600, $user['email']);

            echo json_encode(array('status' => 200, 'token' => 'token_'.$token, 'email' => $user['email']));
        } else {
            echo json_encode(array('status' => 404, 'message' => 'Invalid password'));
        }
    } else {
        echo json_encode(array('status' => 404, 'message' => 'User not found'));
    }

    $stmt->close();
}

$conn->close();
?>
