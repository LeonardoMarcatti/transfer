<?php 
    require_once 'localconection.php';
    setlocale(LC_ALL, "pt_BR.utf-8");

    $sql = "select sigla, nome from tb_uf;";
    $select = $selfie->prepare($sql);
    $select->execute();
    $result = $select->fetchAll(PDO::FETCH_ASSOC);
    $estado = array();

    foreach ($result as $key => $value) {
        $estado[$key] = $value;
    };

    echo 'GetEstados(' . json_encode($estado) . ')';

?>