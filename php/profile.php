<?php

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "1234";
$dbName = "guvi";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function handleGetRequest($email, $conn){
    $stmt = $conn->prepare("SELECT name, email, contact, dob FROM guvi.profile WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $res = array('status' => 200, 'data' => $data);
    $stmt->close();
    return $res;
}

function handlePostRequest($name, $contact, $dob, $email, $conn){
    $stmt = $conn->prepare("UPDATE guvi.profile SET name = ?, contact = ?, dob = ? WHERE email = ?");
    $stmt->bind_param("ssss", $name, $contact, $dob, $email);
    $stmt->execute();
    $res = array('status' => 200, 'data' => "User updated");
    $stmt->close();
    return $res;
}

if (isset($_SERVER['AUTH_TOKEN'])) {
    $email = $redis->get($_SERVER['AUTH_TOKEN']);
    $json_data = array('status' => 404, 'error' => "user not found");
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $json_data = handleGetRequest($email, $conn);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json_data = file_get_contents('php://input');
        $requestData = json_decode($json_data, true);
        $name = $requestData['name'];
        $contact = $requestData['contact'];
        $dob = $requestData['dob'];
        $json_data = handlePostRequest($name, $contact, $dob, $email, $conn);
    }
    header('Content-Type: application/json');
    echo json_encode($json_data);

} else {
    echo json_encode(array('error' => 'Token not provided'));
}

$conn->close();
?>
