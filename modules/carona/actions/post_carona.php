<?php
session_start();
include '../../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hora = $_POST['hora'];
    $data = $_POST['data'];
    $local_saida = trim($_POST['local_saida']);
    $local_chegada = trim($_POST['local_chegada']);
    $valor = floatval($_POST['valor']);

    if (empty($hora) || empty($data) || empty($local_saida) || empty($local_chegada) || $valor < 0) {
        $_SESSION['msg'] = "Todos os campos são obrigatórios.";
        header("Location: ../caronas.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO carona (hora, data, local_saida, local_chegada, valor) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$hora, $data, $local_saida, $local_chegada, $valor]);
        $_SESSION['msg'] = "Carona publicada com sucesso!";
    } catch (Exception $e) {
        $_SESSION['msg'] = "Erro ao publicar.";
    }
}

header("Location: ../caronas.php");
exit;