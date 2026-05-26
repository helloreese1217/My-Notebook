<?php
include 'db.php';

if (isset($_GET['notebook_id'])) {
    $notebook_id = intval($_GET['notebook_id']);

    if ($notebook_id > 0) {
        $stmt_pages = $conn->prepare("DELETE FROM pages WHERE notebook_id = :id");
        $stmt_pages->execute([':id' => $notebook_id]);

        $stmt_notebook = $conn->prepare("DELETE FROM notebooks WHERE id = :id");
        if ($stmt_notebook->execute([':id' => $notebook_id])) {
            header("Location: index.php");
            exit();
        }
    }
}
header("Location: index.php");
exit();
?>