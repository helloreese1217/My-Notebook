<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notebook_name = trim($_POST['notebook_name']);

    if (!empty($notebook_name)) {
        $stmt = $conn->prepare("INSERT INTO notebooks (name) VALUES (:name)");
        
        if ($stmt->execute([':name' => $notebook_name])) {
            header("Location: index.php");
            exit();
        }
    }
}
header("Location: index.php");
exit();
?>