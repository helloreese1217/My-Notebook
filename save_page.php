<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_id = intval($_POST['page_id']);
    $notebook_id = intval($_POST['notebook_id']);
    $page_title = trim($_POST['page_title']);
    $page_content = $_POST['page_content'];

    if ($page_id > 0 && !empty($page_title)) {
        $stmt = $conn->prepare("UPDATE pages SET title = :title, content = :content WHERE id = :id");
        
        $success = $stmt->execute([
            ':title'   => $page_title,
            ':content' => $page_content,
            ':id'      => $page_id
        ]);

        if ($success) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($_SERVER['HTTP_SEC_FETCH_DEST'])) {
                http_response_code(200);
                exit();
            } else {
                header("Location: index.php?notebook_id=" . $notebook_id . "&page_id=" . $page_id);
                exit();
            }
        } else {
            http_response_code(500);
            echo "Database error occurred on save execution.";
            exit();
        }
    }
}
http_response_code(400);
echo "Invalid parameters requested.";
exit();
?>