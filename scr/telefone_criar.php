<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$idPessoa = $_POST['id_pessoa'] ?? '';
$numero = $_POST['numero'] ?? '';

try {
    $pdo = conectar_banco();
    $sql = 'INSERT INTO TELEFONE (ID_PESSOA, NUMERO) VALUES (:id_pessoa, :numero)';
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':id_pessoa', $idPessoa, PDO::PARAM_INT);
    $stm->bindValue(':numero', $numero, PDO::PARAM_STR);
    $stm->execute();
    
    http_response_code(201);
    echo json_encode(['mensagem' => 'Telefone criado']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao criar telefone']);
}
?>