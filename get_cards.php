<?php
include 'db.php';

$query = $pdo->query("SELECT cards.id, cards.title, cards.description, lists.name as list_name 
                      FROM cards JOIN lists ON cards.list_id = lists.id ORDER BY list_id");
$cards = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cards);
?>
