<?php
// Conexão com o banco de dados
$conn = new PDO("mysql:host=localhost;dbname=kanban", "root", "");

// Busca as listas e cards
$lists = $conn->query("SELECT * FROM lists")->fetchAll(PDO::FETCH_ASSOC);
$cards = $conn->query("SELECT * FROM cards")->fetchAll(PDO::FETCH_ASSOC);

// Adicionar card
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCard'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $listId = $_POST['list_id'];

    $stmt = $conn->prepare("INSERT INTO cards (title, description, list_id, updated_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$title, $description, $listId]);

    header("Location: " . $_SERVER['PHP_SELF']); // Redireciona para evitar reenvio do formulário
    exit;
}

// Excluir card
if (isset($_GET['deleteCard'])) {
    $cardId = $_GET['deleteCard'];

    $stmt = $conn->prepare("DELETE FROM cards WHERE id = ?");
    $stmt->execute([$cardId]);

    header("Location: " . $_SERVER['PHP_SELF']); // Redireciona após a exclusão
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban - Portfólio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos para o botão de adicionar card */
        .add-card-btn {
            background-color: #4CAF50; /* Cor de fundo verde */
            color: white; /* Cor do texto branco */
            border: none; /* Sem borda */
            border-radius: 5px; /* Cantos arredondados */
            padding: 10px 15px; /* Espaçamento interno */
            cursor: pointer; /* Cursor pointer ao passar o mouse */
            font-size: 16px; /* Tamanho da fonte */
            margin-top: 10px; /* Margem superior */
            display: inline-block; /* Para ajustar o layout */
        }

        .add-card-form {
            display: none; /* Formulário escondido por padrão */
            margin-top: 10px; /* Margem superior */
        }

        .add-card-form input,
        .add-card-form textarea {
            width: 100%; /* Largura total */
            padding: 10px; /* Espaçamento interno */
            margin-top: 5px; /* Margem superior */
            border: 1px solid #ccc; /* Borda padrão */
            border-radius: 5px; /* Cantos arredondados */
        }

        .add-card-form button {
            background-color: #2196F3; /* Cor de fundo azul */
            color: white; /* Cor do texto branco */
            border: none; /* Sem borda */
            border-radius: 5px; /* Cantos arredondados */
            padding: 10px; /* Espaçamento interno */
            cursor: pointer; /* Cursor pointer ao passar o mouse */
            margin-top: 10px; /* Margem superior */
            width: 100%; /* Largura total */
        }

        .delete-btn {
            background: none; /* Sem fundo */
            border: none; /* Sem borda */
            cursor: pointer; /* Cursor pointer ao passar o mouse */
            color: #f44336; /* Cor do ícone (vermelho) */
            font-size: 16px; /* Tamanho do ícone */
        }

        .delete-btn:hover {
            color: #c62828; /* Cor do ícone ao passar o mouse */
        }
    </style>
</head>
<body>
<h1 class="site-title">Sistema Simples Kanban PHP</h1>

<div class="board">
    <?php foreach ($lists as $list): ?>
        <div class="list" data-id="<?= $list['id'] ?>">
            <h2><?= htmlspecialchars($list['name']) ?></h2>
            <div class="cards">
                <?php foreach ($cards as $card): ?>
                    <?php if ($card['list_id'] == $list['id']): ?>
                        <div class="card" draggable="true" data-id="<?= $card['id'] ?>" ondragstart="handleDragStart(event)">
                            <h3><?= htmlspecialchars($card['title']) ?></h3>
                            <p><?= htmlspecialchars($card['description']) ?></p>
                            <p><small>Última modificação: <?= $card['updated_at'] ?></small></p>
                            <button onclick="openEditModal(<?= $card['id'] ?>, '<?= htmlspecialchars($card['title']) ?>', '<?= htmlspecialchars($card['description']) ?>')">Editar</button>
                            <a href="?deleteCard=<?= $card['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este card?');">
                                <button class="delete-btn" title="Excluir">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <!-- Botão para adicionar novo card -->
            <button class="add-card-btn" onclick="toggleAddCardForm(<?= $list['id'] ?>)">+</button>
            <form action="" method="POST" class="add-card-form" id="form-<?= $list['id'] ?>">
                <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                <input type="text" name="title" placeholder="Título do Card" required>
                <textarea name="description" placeholder="Descrição do Card" required></textarea>
                <button type="submit" name="addCard">Adicionar Card</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal para edição -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Editar Tarefa</h3>
        <input type="hidden" id="editCardId">
        <label for="editTitle">Título</label>
        <input type="text" id="editTitle">
        <label for="editDescription">Descrição</label>
        <textarea id="editDescription"></textarea>
        <button onclick="saveEdit()">Salvar</button>
    </div>
</div>

<script src="app.js"></script>
<script>
    function toggleAddCardForm(listId) {
        const form = document.getElementById(`form-${listId}`);
        form.style.display = form.style.display === 'block' ? 'none' : 'block'; // Alterna a exibição do formulário
    }
</script>

<div class="footer">
    <p>Desenvolvido por <a href="https://github.com/denner-silveira" target="_blank">Denner Silveira</a></p>
</div>
</body>
</html>
