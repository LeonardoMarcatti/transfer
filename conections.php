<?php
    set_time_limit(0);
    header("Access-Control-Allow-Origin: *");
    $server = "206.189.193.229";
    $db = 'db_bigdata';
    $db2 = 'selfie_demonstracao';
    $user = 'postgres';
    $password = 'postmy';
    
    try {
        $bigdata = new PDO("pgsql:host=$server; dbname=$db", "$user", "$password");
    } catch (Throwable $th) {
        echo 'Erro linha: ' . $th->getLine() . "<br>";
        echo ('Código: ' . $th->getMessage()) . "<br>";
    };
   
    try {
        $selfie = new PDO("pgsql:host=$server; dbname=$db2", "$user", "$password");
    } catch (Throwable $th) {
        echo 'Erro linha: ' . $th->getLine() . "<br>";
        echo ('Código: ' . $th->getMessage()) . "<br>";
    };
?>