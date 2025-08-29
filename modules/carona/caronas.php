<?php
session_start();
include '../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Listar caronas
$stmt = $pdo->query("SELECT * FROM carona ORDER BY data DESC, hora ASC");
$caronas = $stmt->fetchAll();

// Filtros (AJAX mais tarde)
$hoje = date('Y-m-d');
$filtro_data = $_GET['data'] ?? '';
$filtro_origem = $_GET['origem'] ?? '';
$filtro_destino = $_GET['destino'] ?? '';

// Aplicar filtros
$sql = "SELECT * FROM carona WHERE 1=1";
$params = [];

if ($filtro_data) {
    $sql .= " AND data = ?";
    $params[] = $filtro_data;
}
if ($filtro_origem) {
    $sql .= " AND local_saida LIKE ?";
    $params[] = "%$filtro_origem%";
}
if ($filtro_destino) {
    $sql .= " AND local_chegada LIKE ?";
    $params[] = "%$filtro_destino%";
}
$sql .= " ORDER BY data DESC, hora ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$caronas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caronas - Área do Estudante</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="dashboard-body">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>🚗 Caronas</h1>
            <button class="btn btn-primary" onclick="openModal('postCaronaModal')">Oferecer Carona</button>
        </header>

        <!-- Filtros -->
        <section class="filters">
            <input type="date" id="filtro-data" placeholder="Data" value="<?php echo $filtro_data; ?>">
            <input type="text" id="filtro-origem" placeholder="Origem" value="<?php echo $filtro_origem; ?>">
            <input type="text" id="filtro-destino" placeholder="Destino" value="<?php echo $filtro_destino; ?>">
            <button onclick="applyFilters()">Filtrar</button>
            <button onclick="clearFilters()">Limpar</button>
        </section>

        <!-- Lista de Caronas -->
        <div class="cards-grid">
            <?php if (count($caronas) > 0): ?>
                <?php foreach ($caronas as $c): ?>
                    <div class="card carona-card">
                        <h3><?php echo htmlspecialchars($c['local_saida']); ?> → <?php echo htmlspecialchars($c['local_chegada']); ?></h3>
                        <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($c['data'])); ?></p>
                        <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($c['hora'])); ?></p>
                        <p><strong>Valor:</strong> R$ <?php echo number_format($c['valor'], 2, ',', '.'); ?></p>
                        <div class="card-footer">
                            <span class="status">Disponível</span>
                            <button class="btn btn-sm btn-success" onclick="contactDriver()">Entrar em Contato</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma carona encontrada.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal de Postagem -->
    <div id="postCaronaModal" class="modal">
        <div class="modal-content">
            <h2>Oferecer Carona</h2>
            <form method="POST" action="actions/post_carona.php">
                <input type="time" name="hora" placeholder="Hora" required>
                <input type="date" name="data" required>
                <input type="text" name="local_saida" placeholder="Local de Saída" required>
                <input type="text" name="local_chegada" placeholder="Destino" required>
                <input type="number" name="valor" placeholder="Valor (R$)" step="0.01" required>
                <div class="modal-buttons">
                    <button type="button" onclick="closeModal('postCaronaModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Publicar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        function applyFilters() {
            const data = document.getElementById('filtro-data').value;
            const origem = document.getElementById('filtro-origem').value;
            const destino = document.getElementById('filtro-destino').value;
            let url = `caronas.php?`;
            if (data) url += `&data=${data}`;
            if (origem) url += `&origem=${origem}`;
            if (destino) url += `&destino=${destino}`;
            window.location.href = url;
        }

        function clearFilters() {
            document.getElementById('filtro-data').value = '';
            document.getElementById('filtro-origem').value = '';
            document.getElementById('filtro-destino').value = '';
            window.location.href = 'caronas.php';
        }

        function contactDriver() {
            alert("Entre em contato: <?php echo $_SESSION['user_nome']; ?> (via app ou telefone)");
        }

        // Fechar modal ao clicar fora
        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; }
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .carona-card .card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; }
        .status { background: #C8E6C9; color: #2e7d32; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; }
        .btn-sm { padding: 6px 12px; font-size: 0.9rem; }
        .filters { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .filters input, .filters button { padding: 10px; }
    </style>
</body>
</html>