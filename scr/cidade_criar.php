<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$cidade = $_POST['cidade'] ?? '';
$estado = $_POST['estado'] ?? '';

try {
    $pdo = conectar_banco();
    $stm = $pdo->prepare('SELECT ID_CIDADE, CIDADE, ESTADO FROM CIDADE WHERE CIDADE = :cidade AND ESTADO = :estado');
    $stm->bindValue(':cidade', $cidade, PDO::PARAM_STR);
    $stm->bindValue(':estado', $estado, PDO::PARAM_STR);
    $stm->execute();
    $existente = $stm->fetch(PDO::FETCH_ASSOC);

    if ($existente) {
        echo json_encode($existente, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stm = $pdo->prepare('INSERT INTO CIDADE (CIDADE, ESTADO) VALUES (:cidade, :estado)');
    $stm->bindValue(':cidade', $cidade, PDO::PARAM_STR);
    $stm->bindValue(':estado', $estado, PDO::PARAM_STR);
    $stm->execute();

    $novoId = $pdo->lastInsertId();
    http_response_code(201);
    echo json_encode(['ID_CIDADE' => $novoId, 'CIDADE' => $cidade, 'ESTADO' => $estado], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao criar cidade']);
}
?>