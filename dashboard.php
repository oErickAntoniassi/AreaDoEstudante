<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_nome = $_SESSION['user_nome'];

// Estatísticas
$caronas_count = $pdo->query("SELECT COUNT(*) FROM carona")->fetchColumn();
$livros_count = $pdo->query("SELECT COUNT(*) FROM livro")->fetchColumn();
$materiais_count = $pdo->query("SELECT COUNT(*) FROM material")->fetchColumn();

// Últimas postagens
$recent_caronas = $pdo->query("SELECT * FROM carona ORDER BY data DESC, hora DESC LIMIT 3")->fetchAll();
$recent_livros = $pdo->query("SELECT * FROM livro ORDER BY cod_livro DESC LIMIT 3")->fetchAll();
$recent_materiais = $pdo->query("SELECT * FROM material ORDER BY cod_item DESC LIMIT 3")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Área do Estudante</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <i data-lucide="graduation-cap"></i>
            <span>Área do Estudante</span>
        </div>
        <nav>
            <a href="dashboard.php" class="active"><i data-lucide="home"></i> Dashboard</a>
            <a href="modules/caronas/caronas.php"><i data-lucide="car"></i> Caronas</a>
            <a href="modules/livros/livros.php"><i data-lucide="book"></i> Livros</a>
            <a href="modules/materiais/materiais.php"><i data-lucide="box"></i> Materiais</a>
        </nav>
        <div class="sidebar-footer">
            <span>Olá, <?php echo htmlspecialchars($user_nome); ?>!</span>
            <a href="includes/logout.php" class="logout"><i data-lucide="log-out"></i> Sair</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="top-bar">
            <h1>Dashboard</h1>
            <div class="user-menu">
                <button id="dark-mode-toggle"><i data-lucide="moon"></i></button>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <i data-lucide="car"></i>
                <div>
                    <h3><?php echo $caronas_count; ?></h3>
                    <p>Caronas Disponíveis</p>
                </div>
            </div>
            <div class="stat-card">
                <i data-lucide="book"></i>
                <div>
                    <h3><?php echo $livros_count; ?></h3>
                    <p>Livros Disponíveis</p>
                </div>
            </div>
            <div class="stat-card">
                <i data-lucide="box"></i>
                <div>
                    <h3><?php echo $materiais_count; ?></h3>
                    <p>Materiais Disponíveis</p>
                </div>
            </div>
        </section>

        <section class="quick-actions">
            <h2>Acesso Rápido</h2>
            <div class="buttons">
                <a href="modules/caronas/caronas.php" class="btn btn-primary">Oferecer Carona</a>
                <a href="modules/livros/livros.php" class="btn btn-success">Vender Livro</a>
                <a href="modules/materiais/materiais.php" class="btn btn-warning">Anunciar Material</a>
            </div>
        </section>

        <section class="recent-feed">
            <h2>Últimas Postagens</h2>
            <div class="feed-cards">
                <?php foreach ($recent_caronas as $c): ?>
                    <div class="card feed-item">
                        <strong>Carona</strong>: <?php echo htmlspecialchars($c['local_saida']); ?> → <?php echo htmlspecialchars($c['local_chegada']); ?>
                        <small><?php echo date('d/m', strtotime($c['data'])); ?> às <?php echo $c['hora']; ?></small>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($recent_livros as $l): ?>
                    <div class="card feed-item">
                        <strong>Livro</strong>: <?php echo htmlspecialchars($l['nome']); ?> (<?php echo htmlspecialchars($l['autor']); ?>)
                        <small>R$ <?php echo number_format($l['valor'], 2, ',', '.'); ?></small>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($recent_materiais as $m): ?>
                    <div class="card feed-item">
                        <strong>Material</strong>: <?php echo htmlspecialchars($m['nome']); ?>
                        <small>R$ <?php echo number_format($m['valor'], 2, ',', '.'); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Dark Mode Toggle
            const toggle = document.getElementById('dark-mode-toggle');
            const body = document.body;
            toggle.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                toggle.innerHTML = body.classList.contains('dark-mode') 
                    ? '<i data-lucide="sun"></i>' 
                    : '<i data-lucide="moon"></i>';
                lucide.createIcons();
            });
        });
    </script>
</body>
</html>