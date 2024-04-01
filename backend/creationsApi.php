<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");

include 'connectDb.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "GET":
        if (isset($_GET['id'])) {
            // Fetch details for a specific product based on the provided ID
            $productId = $_GET['id'];
            $sql = "SELECT * FROM creations WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $creation = $stmt->fetch(PDO::FETCH_ASSOC);

            // Adjust the image path in the result
            if ($creation) {
                $creation['image'] = 'http://localhost/art-gallery/images/' . $creation['image'];
                echo json_encode($creation);
            } else {
                echo json_encode(["error" => "Product not found"]);
            }
        } else {
            // Fetch all products
            $sql = "SELECT * FROM creations";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $creations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Adjust the image paths in the result
            foreach ($creations as &$creation) {
                $creation['image'] = 'http://localhost/art-gallery/images/' . $creation['image'];
            }

            echo json_encode($creations);
        }
        break;
    

        case "POST":
            if (isset($_FILES["image"])) {
                $name = $_POST['name'];
                $material = $_POST['material'];
                $size = $_POST['size'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $image = $_FILES["image"]["name"];
                $image_temp = $_FILES['image']['tmp_name'];
                $destination = $_SERVER['DOCUMENT_ROOT'] . '/art-gallery/images' . "/" . $image;
    
                $sql = "INSERT INTO creations (name,material,size, description,price, image) VALUES (:name,:material,:size, :description,:price, :image)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':material', $material);
                $stmt->bindParam(':size', $size);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':image', $image);
                
                if ($stmt->execute()) {
                    move_uploaded_file($image_temp, $destination);
                    echo json_encode(["success" => "creation inserted successfully"]);
                    return;
                } else {
                    echo json_encode(["fail" => "creation not inserted"]);
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

             $sql = "DELETE FROM creations WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    echo json_encode(["success" => "creation deleted successfully"]);
                } else {
                    echo json_encode(["fail" => "creation not deleted"]);
                }
            } else {
                echo json_encode(["fail" => "Invalid data format for deletion"]);
            }
            break;
}
?>
