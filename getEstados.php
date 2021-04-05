<?php 
    require_once 'conections.php';
    setlocale(LC_ALL, "pt_BR.utf-8");

    $sql = "select id, sigla, nome from tb_uf;";
    $select = $selfie->prepare($sql);
    $select->execute();
    $result = $select->fetchAll(pdo::FETCH_ASSOC);
    $estado = array();

    foreach ($result as $key => $value) {
        $estado[$key] = $value;
    };

    echo 'GetEstados(' . json_encode($estado) . ')';

?>