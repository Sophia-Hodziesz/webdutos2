<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$idPessoa = $_GET['id_pessoa'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $idPessoa) {
    try {
        $pdo = conectar_banco();
        $stm = $pdo->prepare('DELETE FROM TELEFONE WHERE ID_PESSOA = :id_pessoa');
        $stm->bindValue(':id_pessoa', $idPessoa, PDO::PARAM_INT);
        $stm->execute();
        
        echo json_encode(['mensagem' => 'Telefone excluido']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao excluir telefone']);
    }
}
?>