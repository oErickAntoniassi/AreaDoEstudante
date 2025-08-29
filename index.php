<?php
session_start();
include 'includes/config.php';

// Redireciona se já estiver logado
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $nome = trim($_POST['nome']);
    $senha = $_POST['senha'];

    if (!empty($nome) && !empty($senha)) {
        $stmt = $pdo->prepare("SELECT ID, nome, senha, tipo FROM usuario WHERE nome = ?");
        $stmt->execute([$nome]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_tipo'] = $user['tipo'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Nome ou senha inválidos.";
        }
    } else {
        $error = "Preencha todos os campos.";
    }
}

// Processar registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nome = trim($_POST['nome']);
    $senha = $_POST['senha'];
    $confirm_senha = $_POST['confirm_senha'];

    if (empty($nome) || empty($senha)) {
        $error = "Todos os campos são obrigatórios.";
    } elseif ($senha !== $confirm_senha) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $hashed = password_hash($senha, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO usuario (nome, senha, tipo) VALUES (?, ?, 'aluno')");
            $stmt->execute([$nome, $hashed]);
            $success = "Cadastro realizado com sucesso! Faça login.";
        } catch (PDOException $e) {
            $error = "Nome já cadastrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Estudante</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="logo">
            <i data-lucide="graduation-cap"></i>
            <h1>Área do Estudante</h1>
        </div>

        <div class="tabs">
            <button id="login-tab" class="active">Entrar</button>
            <button id="register-tab">Cadastrar</button>
        </div>

        <form method="POST" class="auth-form" id="login-form">
            <input type="text" name="nome" placeholder="Seu nome" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" name="login">Entrar</button>
        </form>

        <form method="POST" class="auth-form hidden" id="register-form">
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="password" name="senha" placeholder="Senha (mín. 6)" required>
            <input type="password" name="confirm_senha" placeholder="Confirmar senha" required>
            <button type="submit" name="register">Cadastrar</button>
        </form>

        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            const loginTab = document.getElementById('login-tab');
            const registerTab = document.getElementById('register-tab');
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            loginTab.addEventListener('click', () => {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
            });

            registerTab.addEventListener('click', () => {
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
            });
        });
    </script>
</body>
</html>