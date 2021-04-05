<?php

use function PHPSTORM_META\type;

require_once 'conections.php'; 
    setlocale(LC_ALL, "pt_BR.utf-8");

    $municipios = filter_input(INPUT_POST, 'municipios', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estados', FILTER_SANITIZE_STRING);

    if ($municipios == '') {
        if ($estado == "0") {
            $sql_qte = "select count(*) from tb_empresa";
        } else{
            $sql_qte = "select count(*) from tb_empresa where uf = :uf";
        };
    } else {
        $sql_qte = "select count(*) from tb_empresa where cod_municipio in ($municipios)";
    };

    $qte = $bigdata->prepare($sql_qte);
    if ($municipios == '') {
        if ($estado != "0") {
            $qte->bindValue(':uf', $estado);
        };
    };

    $qte->execute();
    $result_qte = $qte->fetch()[0];

    $limit = 100;
    $num_de_csv = ceil($result_qte/$limit);

    $zip = new ZipArchive();
    $arquivo_zip = 'agente.zip';
    $zip->open($arquivo_zip, ZipArchive::CREATE);

    for ($i=0; $i < $num_de_csv; $i++) {
        if ($municipios == '') {
            if ($estado == "0") {
                $sql = "select 'J' as J, cnpj_cpf, '-' as nulo1, cep, logradouro, numero, complemento, id,  '-' as nulo2, '-' as nulo3, '-' as nulo4, ddd_1, telefone_1, '-' as nulo5, email, '-' as nulo6, data_inicio_ativ, razao_social, data_situacao, 
                '-' as nulo7, bairro, ddd_2, telefone_2 from tb_empresa order by razao_social offset " . $limit*$i . 'limit ' . $limit; 
            } else{
                $sql = "select 'J' as J, cnpj_cpf, '-' as nulo1, cep, logradouro, numero, complemento, '-' as nulo2, '-' as nulo3, '-' as nulo4, ddd_1, telefone_1, '-' as nulo5, email, '-' as nulo6, data_inicio_ativ, razao_social, data_situacao, 
                '-' as nulo7, bairro, ddd_2, telefone_2 from tb_empresa where uf = :uf order by razao_social offset " . $limit*$i . 'limit ' . $limit;
            };
        } else {
            $sql = "select 'J' as J, cnpj_cpf, '-' as nulo1, cep, logradouro, numero, complemento, m.id as id_municipio,  '-' as nulo2, '-' as nulo3, '-' as nulo4, ddd_1, telefone_1, '-' as nulo5, email, '-' as nulo6, data_inicio_ativ, razao_social, 
            data_situacao, '-' as nulo7, bairro, ddd_2, telefone_2 from tb_empresa e join tb_municipio m on e.cod_municipio = m.cod_municipio where e.cod_municipio in ($municipios) order by razao_social offset " . $limit*$i . ' limit ' . $limit;
        };

        $select = $bigdata->prepare($sql);

        if ($municipios == '') {
            if ($estado != "0") {
                $select->bindValue(':uf', $estado);
            };
        };

        $select->execute(); 
        $result = $select->fetchAll(PDO::FETCH_ASSOC);
        $result2 = [];

        $nome_arquivo = 'agente' . $i . '.csv';
        $arquivo = array();
        $arquivo[$i] = fopen($nome_arquivo, 'w');

        foreach ($result as $key => $array) {
            foreach ($array as $key2 => $value) {
                if ($key2 == 'ddd_1' || $key2 == 'ddd_2') {
                    $v = $value;
                    continue;
                } else if($key2 == 'telefone_1' || $key2 == 'telefone_2'){
                    $v .= $value;
                    $result2[$key][] = $v;
                } else{
                    $result2[$key][] = $value  ;
                };
            };
        };

        foreach ($result2 as $key => $value) {
            fputcsv($arquivo[$i], $value, ';');
        };

        fclose($arquivo[$i]);

        $zip->addFile($nome_arquivo);
    };



    $zip->close();

    for ($i=0; $i < $num_de_csv; $i++) {
        unlink('agente' . $i . '.csv');
    };


?>