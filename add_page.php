<?php
include 'db.php';

if (isset($_GET['notebook_id'])) {
    $notebook_id = intval($_GET['notebook_id']);

    if ($notebook_id > 0) {
        $placeholder_title = "";
        
        $stmt = $conn->prepare("INSERT INTO pages (title, notebook_id, content) VALUES (:title, :notebook_id, '')");
        
        if ($stmt->execute([':title' => $placeholder_title, ':notebook_id' => $notebook_id])) {
            $new_page_id = $conn->lastInsertId();
            
            header("Location: index.php?notebook_id=" . $notebook_id . "&page_id=" . $new_page_id);
            exit();
        }
    }
}
header("Location: index.php");
exit();
?>