<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "1234";
$dbname = "guvi";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json_data = file_get_contents('php://input');
    $requestData = json_decode($json_data, true);
    var_dump($requestData);

    $name = $requestData['name'];
    $email = $requestData['email'];
    $password = $requestData['password'];

    $checkStmt = $conn->prepare("SELECT * FROM guvi.users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();


    if ($checkResult->num_rows > 0) {
        header('Content-Type: application/json');
        $res = array('status' => 409, 'message' => 'Email already in use. Please login or try a different email.');
        echo json_encode($res);
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO guvi.users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashed_password);

        $profileStmt = $conn->prepare("INSERT INTO guvi.profile (name, email) VALUES (?, ?)");
        $profileStmt->bind_param("ss", $name, $email);

        header('Content-Type: application/json');
        $res = array();

        if ($stmt->execute() and $profileStmt->execute()) {
            $res['status'] = 201;
            $res['message'] = "User created";
        } else {
            $res['status'] = 400;
            $res['message'] = "Error: " . $stmt->error;
        }

        echo json_encode($res);

        $stmt->close();
        $profileStmt->close();
    }

    $checkStmt->close();
    $conn->close();
}
?>
