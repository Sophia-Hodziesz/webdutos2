<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? null;
$_PUT = json_decode(file_get_contents('php://input'), true);
$numero = $_PUT['numero'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $id) {
    try {
        $pdo = conectar_banco();
        $stm = $pdo->prepare('UPDATE TELEFONE SET NUMERO = :numero WHERE ID_TELEFONE = :id');
        $stm->bindValue(':numero', $numero, PDO::PARAM_STR);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        
        echo json_encode(['mensagem' => 'Telefone atualizado']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar telefone']);
    }
}
?>