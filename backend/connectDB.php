<?php
class DbConnect{
  private  $servername = 'localhost';
  private  $username = 'root';
  private  $password = '';
  private $basededonnees = "art";
  public function connect() {
    try {
        $conn = new PDO('mysql:host=' .$this->servername .';dbname=' . $this->basededonnees, $this->username, $this->password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (\Exception $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
}
