<?php
$conn = new PDO("mysql:host=localhost;dbname=kanban", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardId = $_POST['card_id'];
    $listId = $_POST['list_id'] ?? null;
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;

    if ($listId) {
        $stmt = $conn->prepare("UPDATE cards SET list_id = :list_id WHERE id = :id");
        $stmt->execute(['list_id' => $listId, 'id' => $cardId]);
    } elseif ($title && $description) {
        $stmt = $conn->prepare("UPDATE cards SET title = :title, description = :description WHERE id = :id");
        $stmt->execute(['title' => $title, 'description' => $description, 'id' => $cardId]);
    }

    $updated_at = $conn->query("SELECT updated_at FROM cards WHERE id = $cardId")->fetchColumn();
    echo json_encode(['status' => 'success', 'updated_at' => $updated_at]);
}
?>
