<?php

$host = 'db:/firebird/data/agenda.fdb';
$user = 'SYSDBA';
$password = 'masterkey';

try {
    $pdo = new PDO("firebird:dbname=$host", $user, $password);
    echo "Conectado com sucesso!";
} catch (PDOException $e) {
    echo $e->getMessage();
}

// teste merge dev
// teste merge master
