<?php
function conectar_banco()
{
    $host = "db";
    $dbname = "agenda_contatos";
    $user = "root";
    $password = "root";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Erro na conexão: " . $e->getMessage();
        exit;
    }
}
?>