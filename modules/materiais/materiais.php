<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$categoria = $_GET['categoria'] ?? '';
$estado = $_GET['estado'] ?? '';
$search = $_GET['q'] ?? '';

$sql = "SELECT * FROM material WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND nome LIKE ?";
    $params[] = "%$search%";
}
if ($categoria) {
    $sql .= " AND conteudo LIKE ?";
    $params[] = "%$categoria%";
}
if ($estado) {
    $sql .= " AND descricao LIKE ?";
    $params[] = "%$estado%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materiais = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materiais - Área do Estudante</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="dashboard-body">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>📦 Materiais</h1>
            <button class="btn btn-warning" onclick="openModal('postMaterialModal')">Anunciar Material</button>
        </header>

        <!-- Filtros -->
        <section class="filters">
            <input type="text" id="search" placeholder="Buscar material..." value="<?php echo htmlspecialchars($search); ?>">
            <select id="categoria">
                <option value="">Todas as categorias</option>
                <option value="Papelaria" <?php echo $categoria === 'Papelaria' ? 'selected' : ''; ?>>Papelaria</option>
                <option value="Eletrônicos" <?php echo $categoria === 'Eletrônicos' ? 'selected' : ''; ?>>Eletrônicos</option>
                <option value="Uniforme" <?php echo $categoria === 'Uniforme' ? 'selected' : ''; ?>>Uniforme</option>
            </select>
            <select id="estado">
                <option value="">Estado</option>
                <option value="Novo" <?php echo $estado === 'Novo' ? 'selected' : ''; ?>>Novo</option>
                <option value="Usado" <?php echo $estado === 'Usado' ? 'selected' : ''; ?>>Usado</option>
                <option value="Danificado" <?php echo $estado === 'Danificado' ? 'selected' : ''; ?>>Danificado</option>
            </select>
            <button onclick="filterMateriais()">Filtrar</button>
        </section>

        <!-- Grid -->
        <div class="cards-grid">
            <?php foreach ($materiais as $m): ?>
                <div class="card">
                    <img src="https://via.placeholder.com/150?text=Material" alt="Item" style="width:100%; border-radius:8px;">
                    <h3><?php echo htmlspecialchars($m['nome']); ?></h3>
                    <p><strong>Categoria:</strong> <?php echo htmlspecialchars($m['conteudo'] ?: 'Geral'); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($m['descricao'] ?: 'Não informado'); ?></p>
                    <p><strong>Preço:</strong> R$ <?php echo number_format($m['valor'], 2, ',', '.'); ?></p>
                    <button class="btn btn-sm btn-success" onclick="contactSeller()">Contatar</button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal -->
    <div id="postMaterialModal" class="modal">
        <div class="modal-content">
            <h2>Anunciar Material</h2>
            <form method="POST" action="actions/post_material.php">
                <input type="text" name="nome" placeholder="Nome do Item" required>
                <input type="text" name="conteudo" placeholder="Categoria (ex: Papelaria)">
                <input type="text" name="descricao" placeholder="Estado (Novo, Usado, etc)">
                <input type="number" name="valor" placeholder="Valor (R$)" step="0.01" required>
                <div class="modal-buttons">
                    <button type="button" onclick="closeModal('postMaterialModal')">Cancelar</button>
                    <button type="submit" class="btn-warning">Publicar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterMateriais() {
            const q = document.getElementById('search').value;
            const cat = document.getElementById('categoria').value;
            const est = document.getElementById('estado').value;
            let url = 'materiais.php';
            const params = [];
            if (q) params.push('q=' + encodeURIComponent(q));
            if (cat) params.push('categoria=' + cat);
            if (est) params.push('estado=' + est);
            window.location.href = url + (params.length ? '?' + params.join('&') : '');
        }
    </script>
</body>
</html>