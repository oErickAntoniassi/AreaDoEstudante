<?php
header('Content-Type: application/json');
include '../includes/config.php';

$q = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'livro';

$results = [];

if ($q) {
    if ($type === 'livro') {
        $stmt = $pdo->prepare("SELECT nome, autor, valor FROM livro WHERE nome LIKE ? OR autor LIKE ? LIMIT 5");
        $stmt->execute(["%$q%", "%$q%"]);
        while ($row = $stmt->fetch()) {
            $results[] = ['label' => $row['nome'] . ' - ' . $row['autor'], 'value' => $row['nome']];
        }
    }
}

echo json_encode($results);