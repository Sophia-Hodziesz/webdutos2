<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

try {
    $pdo = conectar_banco();

    if ($metodo === 'POST') {
        $idPessoa = $_POST['id_pessoa'] ?? null;
        $idCidade = $_POST['id_cidade'] ?? null;
        $cep = $_POST['cep'] ?? '';
        $rua = $_POST['rua'] ?? '';
        $bairro = $_POST['bairro'] ?? '';
        $numero = $_POST['numero'] ?? '';

        $sql = 'INSERT INTO ENDERECO (ID_PESSOA, ID_CIDADE, CEP, RUA, BAIRRO, NUMERO) VALUES (:id_pessoa, :id_cidade, :cep, :rua, :bairro, :numero)';
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':id_pessoa', $idPessoa, PDO::PARAM_INT);
        $stm->bindValue(':id_cidade', $idCidade, PDO::PARAM_INT);
        $stm->bindValue(':cep', $cep, PDO::PARAM_STR);
        $stm->bindValue(':rua', $rua, PDO::PARAM_STR);
        $stm->bindValue(':bairro', $bairro, PDO::PARAM_STR);
        $stm->bindValue(':numero', $numero, PDO::PARAM_STR);
        $stm->execute();

        http_response_code(201);
        echo json_encode(['mensagem' => 'Endereço criado com sucesso']);
        exit;
    }

    if ($metodo === 'PUT') {
        $_PUT = json_decode(file_get_contents('php://input'), true);
        $idCidade = $_PUT['id_cidade'] ?? null;
        $cep = $_PUT['cep'] ?? '';
        $rua = $_PUT['rua'] ?? '';
        $bairro = $_PUT['bairro'] ?? '';
        $numero = $_PUT['numero'] ?? '';

        $sql = 'UPDATE ENDERECO SET ID_CIDADE = :id_cidade, CEP = :cep, RUA = :rua, BAIRRO = :bairro, NUMERO = :numero WHERE ID_ENDERECO = :id';
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':id_cidade', $idCidade, PDO::PARAM_INT);
        $stm->bindValue(':cep', $cep, PDO::PARAM_STR);
        $stm->bindValue(':rua', $rua, PDO::PARAM_STR);
        $stm->bindValue(':bairro', $bairro, PDO::PARAM_STR);
        $stm->bindValue(':numero', $numero, PDO::PARAM_STR);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();

        echo json_encode(['mensagem' => 'Endereço atualizado com sucesso']);
        exit;
    }

    if ($metodo === 'GET') {
        $sql = 'SELECT E.ID_ENDERECO, E.CEP, E.RUA, E.BAIRRO, E.NUMERO,
                       P.ID_PESSOA, P.NOME AS NOME_PESSOA,
                       C.ID_CIDADE, C.CIDADE AS NOME_CIDADE, C.ESTADO,
                       T.NUMERO AS TELEFONE
                FROM ENDERECO E
                INNER JOIN PESSOA P ON E.ID_PESSOA = P.ID_PESSOA
                LEFT JOIN CIDADE C ON E.ID_CIDADE = C.ID_CIDADE
                LEFT JOIN TELEFONE T ON P.ID_PESSOA = T.ID_PESSOA';

        $stm = $pdo->prepare($sql);
        $stm->execute();
        echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($metodo === 'DELETE') {
        $stm = $pdo->prepare('DELETE FROM ENDERECO WHERE ID_ENDERECO = :id');
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        echo json_encode(['mensagem' => 'Endereço excluído']);
        exit;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno', 'detalhe' => $e->getMessage()]);
}
?>