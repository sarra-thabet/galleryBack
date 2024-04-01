<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE,PUT, OPTIONS");

include 'connectDb.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "GET":
        $sql = "SELECT * FROM artpiece";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $art = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Adjust the image paths in the result
        foreach ($art as &$artpiece) {
            $artpiece['image'] = 'http://localhost/art-gallery/images/' . $artpiece['image'];
        }

        echo json_encode($art);
        break;


        case "POST":
            if (isset($_FILES["image"])) {
                $title = $_POST['title'];
                $description = $_POST['description'];
                $image = $_FILES["image"]["name"];
                $image_temp = $_FILES['image']['tmp_name'];
                $destination = $_SERVER['DOCUMENT_ROOT'] . '/art-gallery/images' . "/" . $image;
    
                $sql = "INSERT INTO artpiece (title, description, image) VALUES (:title, :description, :image)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':image', $image);
    
                if ($stmt->execute()) {
                    move_uploaded_file($image_temp, $destination);
                    echo json_encode(["success" => "art piece inserted successfully"]);
                    return;
                } else {
                    echo json_encode(["fail" => "art piece not inserted"]);
                    return;
                }
            } else {
                echo json_encode(["fail" => "data not in correct format"]);
                return;
            }
            break;
            case "DELETE":
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        $sql = "DELETE FROM artpiece WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Art piece deleted successfully"]);
        } else {
            echo json_encode(["fail" => "Art piece not deleted"]);
        }
    } else {
        echo json_encode(["fail" => "Invalid data format for deletion"]);
    }
    break;
    case "PUT":
        // Assuming the ID is sent as a query parameter
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        // Check if ID is provided
        if ($id === null) {
            echo json_encode(["fail" => "No ID provided"]);
            return;
        }
        
        // Assuming you receive JSON data with title, description, and optionally image
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['title']) && isset($data['description'])) {
            $title = $data['title'];
            $description = $data['description'];
            // Check if image is set in the update data, if not keep the existing image
            $image = isset($_FILES["image"]) ? $_FILES["image"]["name"] : null;
            $image_temp = isset($_FILES["image"]) ? $_FILES["image"]["tmp_name"] : null;
            
            $sql = "UPDATE artpiece SET title = :title, description = :description";
            // If image is set, update image field as well
            if ($image !== null) {
                $sql .= ", image = :image";
            }
            $sql .= " WHERE id = :id";
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            // If image is set, move the uploaded file and bind image parameter
            if ($image !== null) {
                $destination = $_SERVER['DOCUMENT_ROOT'] . '/art-gallery/images' . "/" . $image;
                move_uploaded_file($image_temp, $destination);
                $stmt->bindParam(':image', $image);
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
            if ($stmt->execute()) {
                echo json_encode(["success" => "Art piece updated successfully"]);
            } else {
                echo json_encode(["fail" => "Art piece not updated"]);
            }
        } else {
            echo json_encode(["fail" => "Invalid data format for update"]);
        }
        break;
    
}
?>
