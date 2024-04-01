<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

include 'connectDb.php';

$objDb = new DbConnect;
$conn = $objDb->connect();

$edata = file_get_contents("php://input");
$dData = json_decode($edata, true);
$admin = $dData['user'];
$pass = $dData['pass'];
$result = "";

if ($admin != "" and $pass != "") {
    $sql = "SELECT * FROM admin WHERE user=:admin AND pass=:pass";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':admin', $admin);
    $stmt->bindParam(':pass', $pass);

    try {
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $result = json_encode(['status' => 1, 'message' => 'Login successful']);
        } else {
            $result = json_encode(['status' => 0, 'message' => 'Incorrect username or password']);
        }
    } catch (PDOException $e) {
        $result = json_encode(['status' => 0, 'message' => 'Database error']);
    }
} else {
    $result = json_encode(['status' => 0, 'message' => 'Invalid username or password']);
}

echo $result;
?>
