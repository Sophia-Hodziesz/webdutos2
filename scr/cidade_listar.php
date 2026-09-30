<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = conectar_banco();
    $stm = $pdo->prepare('SELECT ID_CIDADE, CIDADE, ESTADO FROM CIDADE ORDER BY CIDADE');
    $stm->execute();
    echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao listar cidades']);
}
?>