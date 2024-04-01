<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");


include 'connectDb.php';
$objDb= new DbConnect;
$conn=$objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method){
    case "GET":
        $sql="SELECT * FROM orders";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
        $orders=$stmt->fetchAll(PDO::FETCH_ASSOC);
        
     json_encode($orders);
     echo json_encode($orders);
        break;
        
    case "POST":
        $orders = json_decode(file_get_contents('php://input'));
        $sql= "INSERT INTO orders(fname,lname,email,number)VALUES(:fname,:lname,:email,:number)";
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':fname',$orders->fname);
        $stmt->bindParam(':lname',$orders->lname);
        $stmt->bindParam(':email',$orders->email);
        $stmt->bindParam(':number',$orders->number);
   
        
        if($stmt->execute()){
            echo "$response=(['order Created Successfully'])";
        }
        else{
           echo "$response=(['status'=>0,'message'=>'failed to add'])";
        }break;
    case "DELETE":
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['id'])) {
                $id = $data['id'];
        
                $sql = "DELETE FROM orders WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
                if ($stmt->execute()) {
                    echo json_encode(["success" => "order deleted successfully"]);
                } else {
                    echo json_encode(["fail" => "order not deleted"]);
                }
            } else {
                echo json_encode(["fail" => "Invalid data format for deletion"]);
            }
            break;
}
