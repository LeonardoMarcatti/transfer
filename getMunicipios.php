<?php 
    require_once 'localconection.php'; 
    setlocale(LC_ALL, "pt_BR.utf-8");

    $estado = filter_input(INPUT_POST, 'estados_modal', FILTER_SANITIZE_STRING); 
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cod = filter_input(INPUT_POST, 'cod', FILTER_SANITIZE_NUMBER_INT);
    $municipios = array();

    if ($cod == "") {
        if ($estado != "0" && $nome != "") {
            $sql = "select municipio, cod_municipio, uf from tb_empresa where municipio = :municipio and uf = :uf limit 1";        
        } elseif($estado == "0" && $nome == ""){
            $sql = "select municipio, cod_municipio, uf from tb_empresa";
        } elseif($estado == "0" && $nome != ""){
            $sql = "select distinct(municipio), cod_municipio, uf from tb_empresa where uf 
            in(select distinct(uf) from tb_empresa  where NOT uf = 'EX') and municipio = :municipio";
        } else{
            $sql = "select distinct(municipio), cod_municipio, uf from tb_empresa where uf = :uf"; 
        };
    } else {
        $sql = "select distinct(municipio), cod_municipio, uf from tb_empresa where cod_municipio = :cod limit 1"; 
    };
    
    $select = $bigdata->prepare($sql);

    if ($cod == "") {
        if ($estado != "0" && $nome != "") {
            $select->bindValue(':municipio', strtoupper($nome));
            $select->bindValue(':uf', $estado);
        }elseif($estado == "0" && $nome != ""){
            $select->bindValue(':municipio', strtoupper($nome));
        } else{
            $select->bindValue(':uf', $estado);
        };
    } else {
        $select->bindValue(':cod', $cod);
    };

    $select->execute();
    $result = $select->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $key => $value) {
        $municipios[$key] = $value;
    };

    echo json_encode($municipios);
?>