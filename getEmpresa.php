<?php
    require_once ('localconection.php'); 

    setlocale(LC_ALL, "pt_BR.utf-8");

    $municipios = filter_input(INPUT_POST, 'municipios', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estados', FILTER_SANITIZE_STRING);

    function getSituacao($val)
    {
        global $bigdata;
        $sql = 'select situacao_fk, data_situacao from tb_empresa where cnpj_cpf = :cnpj_cpf';
        $result = $bigdata->prepare($sql);
        $result->bindValue(':cnpj_cpf', $val);
        $result->execute();
        $situacao = $result->fetch(PDO::FETCH_ASSOC);
        return ($situacao['situacao_fk'] == 8) ? $situacao['data_situacao'] : null;
    };

    function getCapitalSocial($val)
    {
        global $bigdata;
        $sql = 'select situacao_fk, data_situacao from tb_empresa where cnpj_cpf = :cnpj_cpf';
        $result = $bigdata->prepare($sql);
        $result->bindValue(':cnpj_cpf', $val);
        $result->execute();
        $situacao = $result->fetch(PDO::FETCH_ASSOC);
        return ($situacao['situacao_fk'] == 8) ? $situacao['data_situacao'] : null;
    };

    if ($municipios == '') {
        if ($estado == "0") {
            $sql_qte = "select count(*) from tb_empresa";
        } else{
            $sql_qte = "select count(*) from tb_empresa where uf = :uf";
        };
    } else {
        $sql_qte = "select count(*) from tb_empresa where cod_municipio in (6243, 5331, 522)";
    };

    $qte = $bigdata->prepare($sql_qte);
    if ($municipios == '') {
        if ($estado != "0") {
            $qte->bindValue(':uf', $estado);
        };
    };

    $qte->execute();
    $result_qte = $qte->fetch()[0];

    $limit = 10000;
    $num_de_csv = ceil($result_qte/$limit);


    //Pega o id do múnicipio e coloca em um array que será usado posteriormente
    if ($municipios == '') {
        if ($estado == "0") {
            $sql_ids = "select id from tb_municipio order by id";
        } else{
            $sql_ids = "select id from tb_municipio where uf = :uf order by id";
        };
    } else {
        $sql_ids = "select id from tb_municipio where cod_municipio in (6243, 5331, 522) order by id";
    };

    $select_ids = $bigdata->prepare($sql_ids);

    if ($municipios == '') {
        if ($estado != "0") {
            $select_ids->bindValue(':uf', $estado);
        };
    };

    $select_ids->execute();    
    $result_ids = $select_ids->fetchAll(PDO::FETCH_ASSOC);


    foreach ($result_ids as $key => $value) {
        for ($i=0; $i < $num_de_csv; $i++) {
            if ($municipios == '') {
                if ($estado == "0") {
                    $sql = "select id, '' as nulo1, '' as nulo2 from tb_agente order by id offset " . $limit*$i . 'limit ' . $limit; 
                } else{
                    $sql = "select id, '' as nulo1, '' as nulo2 from tb_agente where uf = :uf order by id offset " . $limit*$i . 'limit ' . $limit;
                };
            } else {
                $sql = "select id, '' as nulo1, '' as nulo2, cpf_cnpj, '' as nulo3, '' as nulo4, '' as nulo5, '' as nulo6, '' as nulo7 from tb_agente where municipio_fk = $value[id] order by id offset " . $limit*$i . ' limit ' . $limit;
            };
            
            $select = $selfie->prepare($sql);
    
            if ($municipios == '') {
                if ($estado != "0") {
                    $select->bindValue(':uf', $estado);
                };
            };
            
            $result2 = [];

            $select->execute(); 
            $result = $select->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $key1 => $value) {
                foreach ($value as $key2 => $item) {
                    if ($key2 == 'cpf_cnpj') {
                        $result2[$key1][] = getSituacao($item);
                        continue;
                    };
                    $result2[$key1][] =  $item;       
                };
            };
            
        };
    };

?>