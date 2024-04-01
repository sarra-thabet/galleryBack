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
        $sql="SELECT * FROM clients";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
        $clients=$stmt->fetchAll(PDO::FETCH_ASSOC);
        
     json_encode($clients);
     echo json_encode($clients);
        break;
        
    case "POST":
        $clients = json_decode(file_get_contents('php://input'));
        $sql= "INSERT INTO clients(fname,lname,email,number,message)VALUES(:fname,:lname,:email,:number,:message)";
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':fname',$clients->fname);
        $stmt->bindParam(':lname',$clients->lname);
        $stmt->bindParam(':email',$clients->email);
        $stmt->bindParam(':number',$clients->number);
        $stmt->bindParam(':message',$clients->message);
        
        if($stmt->execute()){
            echo "$response=(['client Created Successfully'])";
        }
        else{
           echo "$response=(['status'=>0,'message'=>'failed to add'])";
        }break;
    case "DELETE":
            $data = json_decode(file_get_contents("php://input"), true);
        
            if (isset($data['id'])) {
                $id = $data['id'];
        
                $sql = "DELETE FROM clients WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
                if ($stmt->execute()) {
                    echo json_encode(["success" => "Client deleted successfully"]);
                } else {
                    echo json_encode(["fail" => "Client not deleted"]);
                }
            } else {
                echo json_encode(["fail" => "Invalid data format for deletion"]);
            }
            break;
}

