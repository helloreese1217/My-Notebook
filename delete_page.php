<?php
include 'db.php';

if (isset($_GET['page_id']) && isset($_GET['notebook_id'])) {
    $page_id = intval($_GET['page_id']);
    $notebook_id = intval($_GET['notebook_id']);

    if ($page_id > 0) {
        $stmt = $conn->prepare("DELETE FROM pages WHERE id = :id");
        if ($stmt->execute([':id' => $page_id])) {
            header("Location: index.php?notebook_id=" . $notebook_id);
            exit();
        }
    }
}
header("Location: index.php");
exit();
?>