<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

try {
    $pdo = conectar_banco();

    if ($metodo === 'POST') {
        $nome = $_POST['nome'] ?? '';
        if (empty($nome)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome é obrigatório']);
            exit;
        }

        $stm = $pdo->prepare('INSERT INTO PESSOA (NOME) VALUES (:nome)');
        $stm->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stm->execute();

        $novoId = $pdo->lastInsertId();
        http_response_code(201);
        echo json_encode(['ID_PESSOA' => $novoId, 'NOME' => $nome]);
        exit;
    }

    if ($metodo === 'PUT') {
        $_PUT = json_decode(file_get_contents('php://input'), true);
        $nome = $_PUT['nome'] ?? '';

        $stm = $pdo->prepare('UPDATE PESSOA SET NOME = :nome WHERE ID_PESSOA = :id');
        $stm->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();

        echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso']);
        exit;
    }

    if ($metodo === 'DELETE') {
        $stm = $pdo->prepare('DELETE FROM PESSOA WHERE ID_PESSOA = :id');
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();

        echo json_encode(['mensagem' => 'Pessoa excluída com sucesso']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno', 'detalhe' => $e->getMessage()]);
}
?>