<?php
// Include your database connection code here
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abhi";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if (isset($_GET["id"])) {
    // Fetch the plan to delete
    $id = $_GET["id"];
    
    $sql = "DELETE FROM washing_plans WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: display.php"); // Redirect to the plan list page
    exit();
}
?>
