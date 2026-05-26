<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $notebook_id = isset($data['id']) ? intval($data['id']) : 0;
    $new_name = isset($data['name']) ? trim($data['name']) : '';

    if ($notebook_id > 0 && $new_name !== '') {
        try {
            $stmt = $conn->prepare("UPDATE notebooks SET name = :name WHERE id = :id");
            $stmt->execute([
                ':name' => $new_name,
                ':id' => $notebook_id
            ]);
            
            http_response_code(200);
            echo json_encode(['success' => true]);
            exit();
        } catch (PDOException $e) {
            http_response_code(500);
            exit();
        }
    }
}
http_response_code(400);