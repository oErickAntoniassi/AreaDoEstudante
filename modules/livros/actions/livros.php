<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Filtros
$tipo = $_GET['tipo'] ?? '';
$autor = $_GET['autor'] ?? '';
$search = $_GET['q'] ?? '';

$sql = "SELECT * FROM livro WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (nome LIKE ? OR autor LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($autor) {
    $sql .= " AND autor LIKE ?";
    $params[] = "%$autor%";
}
if ($tipo === 'venda') {
    $sql .= " AND valor > 0";
} elseif ($tipo === 'doacao') {
    $sql .= " AND valor = 0";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros - Área do Estudante</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="dashboard-body">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>📚 Livros</h1>
            <button class="btn btn-success" onclick="openModal('postLivroModal')">Vender/Trocar/Doar</button>
        </header>

        <!-- Filtros -->
        <section class="filters">
            <input type="text" id="search" placeholder="Buscar livro ou autor..." value="<?php echo htmlspecialchars($search); ?>">
            <input type="text" id="filtro-autor" placeholder="Autor" value="<?php echo htmlspecialchars($autor); ?>">
            <select id="filtro-tipo">
                <option value="">Todos</option>
                <option value="venda" <?php echo $tipo === 'venda' ? 'selected' : ''; ?>>Venda</option>
                <option value="doacao" <?php echo $tipo === 'doacao' ? 'selected' : ''; ?>>Doação</option>
            </select>
            <button onclick="filterLivros()">Filtrar</button>
        </section>

        <!-- Grid de Livros -->
        <div class="cards-grid">
            <?php foreach ($livros as $l): ?>
                <div class="card">
                    <img src="https://via.placeholder.com/150?text=Livro" alt="Capa" style="width:100%; border-radius:8px;">
                    <h3><?php echo htmlspecialchars($l['nome']); ?></h3>
                    <p><strong>Autor:</strong> <?php echo htmlspecialchars($l['autor']); ?></p>
                    <p><strong>Preço:</strong> 
                        <?php echo $l['valor'] > 0 ? 'R$ ' . number_format($l['valor'], 2, ',', '.') : 'Grátis (Doação)'; ?>
                    </p>
                    <span class="badge <?php echo $l['valor'] > 0 ? 'sale' : 'donate'; ?>">
                        <?php echo $l['valor'] > 0 ? 'Venda' : 'Doação'; ?>
                    </span>
                    <button class="btn btn-sm btn-success" style="margin-top:10px;" onclick="contactSeller()">Contatar</button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal de Postagem -->
    <div id="postLivroModal" class="modal">
        <div class="modal-content">
            <h2>Publicar Livro</h2>
            <form method="POST" action="actions/post_livro.php" enctype="multipart/form-data">
                <input type="text" name="nome" placeholder="Título do Livro" required>
                <input type="text" name="autor" placeholder="Autor" required>
                <textarea name="conteudo" placeholder="Descrição (estado, edição, etc.)"></textarea>
                <input type="number" name="valor" placeholder="Valor (0 para doação)" step="0.01">
                <div class="modal-buttons">
                    <button type="button" onclick="closeModal('postLivroModal')">Cancelar</button>
                    <button type="submit" class="btn-success">Publicar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterLivros() {
            const q = document.getElementById('search').value;
            const autor = document.getElementById('filtro-autor').value;
            const tipo = document.getElementById('filtro-tipo').value;
            let url = 'livros.php';
            const params = [];
            if (q) params.push('q=' + encodeURIComponent(q));
            if (autor) params.push('autor=' + encodeURIComponent(autor));
            if (tipo) params.push('tipo=' + tipo);
            window.location.href = url + (params.length ? '?' + params.join('&') : '');
        }

        function contactSeller() {
            alert("Entre em contato com o vendedor!");
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    <style>
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; color: white; }
        .sale { background: #4CAF50; }
        .donate { background: #FF9800; }
    </style>
</body>
</html>