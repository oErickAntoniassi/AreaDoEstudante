<?php
$user_nome = $_SESSION['user_nome'] ?? 'Usuário';
?>
<aside class="sidebar">
    <div class="logo">
        <i data-lucide="graduation-cap"></i>
        <span>Área do Estudante</span>
    </div>
    <nav>
        <a href="../dashboard.php"><i data-lucide="home"></i> Dashboard</a>
        <a href="caronas/caronas.php"><i data-lucide="car"></i> Caronas</a>
        <a href="livros/livros.php"><i data-lucide="book"></i> Livros</a>
        <a href="materiais/materiais.php"><i data-lucide="box"></i> Materiais</a>
    </nav>
    <div class="sidebar-footer">
        <span>Olá, <?php echo htmlspecialchars($user_nome); ?>!</span>
        <a href="../includes/logout.php" class="logout"><i data-lucide="log-out"></i> Sair</a>
    </div>
</aside>