<?php

    require_once 'localconection.php';
    require_once 'classes/Agente.php';
    require_once 'classes/Empresa.php';
    require_once 'classes/CnaeSecundario.php';
    require_once 'classes/Socio.php';
    use classes\SocioDAO;
    use classes\AgenteDAO;
    use classes\EmpresaDAO;
    use classes\CnaeSecundarioDAO;
    setlocale(LC_ALL, "pt_BR.utf-8");

    $municipios = filter_input(INPUT_POST, 'municipios', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estados', FILTER_SANITIZE_STRING);

    $agenteDAO = new AgenteDAO($selfie);
    $id_agente = new AgenteDAO($selfie);
    $id_empresa = new CnaeSecundarioDAO($selfie);
    $cnaes = new CnaeSecundarioDAO($bigdata);
    $cnae_selfie = new CnaeSecundarioDAO($selfie);
    $socio = new SocioDAO($bigdata);
    $empresa2 = new EmpresaDAO($selfie);
    $empresa = new EmpresaDAO($bigdata);
    $empresa_csv = array();
    $lista_id_agentes = array();
    $lista_cnpj = array();
    $id_agentes = array();
    $id_empresas = array();
    $lista_cnaes = array();
    $socios = array();
       

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

    $limit = 100000;
    $num_de_csv = ceil($result_qte/$limit); //Quantidade de arquivos csv necessários

    for ($i=0; $i < $num_de_csv; $i++) {
        $offset = $limit * $i;
        if ($municipios == '') {
            if ($estado == "0") {
                $agente = new AgenteDAO($bigdata);
                $result = $agente->getAgenteBrasil($limit, $offset);
            } else{
                $agente = new AgenteDAO($bigdata);
                $result = $agente->getAgenteEstado($estado, $limit, $offset);
            };
        } else {
            $agente = new AgenteDAO($bigdata);
            $result =  $agente->getAgenteMunicipios($municipios, $limit, $offset);
        };

        $nome_arquivo = 'agente' . $i . '.csv';
        $arquivo[$i] = fopen($nome_arquivo, 'w');
        $result2 = array();

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
        rename($nome_arquivo, 'CSVs/agente/' . $nome_arquivo);
    };

    for ($i2=0; $i2 < $num_de_csv; $i2++) {
        $CSVfile = 'agente' . $i2 . '.csv';
        $handle = fopen('CSVs/agente/' . $CSVfile, 'r');
        if ($handle) {
            while ($data2 = fgetcsv($handle, 1000, ';')) {
                $data = array();
                for ($i3=0; $i3 < count($data2); $i3++) { 
                    if ($data2[$i3] == '') {
                        // roberto mão recatada e broxante
                        $data2[$i3] = null;
                    };
                    array_push($data, $data2[$i3]);
                };
                $agenteDAO = new AgenteDAO($selfie);
                $return_id = $agenteDAO->checkAgente($data[1]);
                if ($return_id) {
                    $agenteDAO->updateAgente($return_id, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13], $data[14], $data[15], $data[16], $data[17], $data[18], $data[19], $data[20]);
                } else {
                   $agenteDAO->addAgente($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13], $data[14], $data[15], $data[16], $data[17], $data[18], $data[19], $data[20]);
                };

            };    
        };
        fclose($handle);
    };

    //lista agentes
    $ids_municipios = $empresa->getIDMunicipio([5331, 6243]);
    foreach ($ids_municipios as $key => $value) {
        $lista_parcial = $empresa2->getAgenteFK($value);
        foreach ($lista_parcial as $key => $value) {
            array_push($lista_id_agentes, $value['id']);
        };
    };

    //Organiza a lista em ordem crescente de ids
    sort($lista_id_agentes);

    //lista cnpj_cpf
    for ($i4=0; $i4 < $num_de_csv; $i4++) {
        $CSVfile = 'agente' . $i4 . '.csv';
        $handle = fopen('CSVs/agente/' . $CSVfile, 'r');
        if ($handle) {
            while ($data = fgetcsv($handle, 1000, ';')) {
                array_push($lista_cnpj, $data[1]);
            };    
        };
        fclose($handle);
    };

    for ($i5=0; $i5 < count($lista_cnpj); $i5++) { 
        $empresa_csv[$i5][] = $lista_id_agentes[$i5];
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = $empresa->getDataFechamento($lista_cnpj[$i5]);
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = $empresa->getInfo($lista_cnpj[$i5])[0]['capital_social'];
        $empresa_csv[$i5][] = (strlen($empresa->getInfo($lista_cnpj[$i5])[0]['cnae_fiscal']) == 6) ? 0 . $empresa->getInfo($lista_cnpj[$i5])[0]['cnae_fiscal'] : $empresa->getInfo($lista_cnpj[$i5])[0]['cnae_fiscal'];
        $empresa_csv[$i5][] = ($empresa->getInfo($lista_cnpj[$i5])[0]['nome_fantasia']) ? $empresa->getInfo($lista_cnpj[$i5])[0]['nome_fantasia'] : '-';
        $empresa_csv[$i5][] = $empresa->getRegimeFK($lista_cnpj[$i5]);
        $empresa_csv[$i5][] = $empresa->getPorteFK($lista_cnpj[$i5]);
        $empresa_csv[$i5][] = $empresa->getInfo($lista_cnpj[$i5])[0]['cod_nat_juridica'];
        $empresa_csv[$i5][] = null;
        $empresa_csv[$i5][] = $empresa->getSituacaoFK($lista_cnpj[$i5]);
        $empresa_csv[$i5][] = $empresa->getSeguimentoFK($lista_cnpj[$i5]);
        $empresa_csv[$i5][] = $empresa->getInfo($lista_cnpj[$i5])[0]['data_situacao'];
    };

    $csv = fopen('empresa.csv', 'w');
    foreach ($empresa_csv as $key => $value) {
        fputcsv($csv, $value, ';');
    };

    fclose($csv);
    rename('empresa.csv', 'CSVs/empresa/empresa.csv');

    $grava_empresa = fopen('CSVs/empresa/empresa.csv', 'r');
    if ($grava_empresa) {
        while ($data2 = fgetcsv($grava_empresa, 1000, ';')) {
            $data = array();
            for ($i5=0; $i5 < count($data2); $i5++) { 
                if ($data2[$i5] == '') {
                   $data2[$i5] = null;
                };
            };
            array_push($data, $data2);
            $empresaDAO = new EmpresaDAO($selfie);
            $id = $empresaDAO->checkEmpresa($data[0][0]);
            if ($id) {
                $empresaDAO->updateEmpresa($id, $data[0][0], $data[0][1], $data[0][2], $data[0][3], $data[0][4], $data[0][5], $data[0][6], $data[0][7], $data[0][8], $data[0][9], $data[0][10], $data[0][11], $data[0][12], $data[0][13], $data[0][14], $data[0][15], $data[0][16], $data[0][17], $data[0][18]);
            } else {
                $empresaDAO->addEmpresa($data[0][0], $data[0][1], $data[0][2], $data[0][3], $data[0][4], $data[0][5], $data[0][6], $data[0][7], $data[0][8], $data[0][9], $data[0][10], $data[0][11], $data[0][12], $data[0][13], $data[0][14], $data[0][15], $data[0][16], $data[0][17], $data[0][18]);
            };
        };
        fclose($grava_empresa);
    } else{
        echo 'erro';
    };

    for ($i6=0; $i6 < $num_de_csv; $i6++) {
        $CSVfile = 'agente' . $i6 . '.csv';
        $handle = fopen('CSVs/agente/' . $CSVfile, 'r');
        if ($handle) {
            while ($data = fgetcsv($handle, 1000, ';')) {
                array_push($lista_cnpj, $data[1]);
            };    
        };
        fclose($handle);
    };

    foreach ($lista_cnpj as $key => $value) {
        $id = $id_agente->checkAgente($value);
        array_push($id_agentes, $id);
    };

    foreach ($id_agentes as $key => $value) {
        $id = $id_empresa->getEmpresaID($value);
        array_push($id_empresas, $id);
    };


    //Le a lista de CNPJs e preenche o array. Em alguns casos preenche com um 0 o inicio do cnae
     for ($i=0; $i < count($lista_cnpj) ; $i++) {
        $cnae = $cnaes->getCNAES($lista_cnpj[$i]);
        for ($i2=0; $i2 < count($cnae); $i2++) { 
            if (strlen($cnae[$i2]['cnae_secundario']) == 6) {
                $lista_cnaes[] = [$id_empresas[$i],  '0' . $cnae[$i2]['cnae_secundario']];
            } else {
                $lista_cnaes[] = [$id_empresas[$i], $cnae[$i2]['cnae_secundario']]; 
            };
        };
    };

    //preenche o CSV
    $csv = fopen('cnaes.csv', 'w');
    foreach ($lista_cnaes as $key => $value) {
        fputcsv($csv, $value, ';');
    };

    fclose($csv);
    rename('cnaes.csv', 'CSVs/cnae_sec/cnaes.csv');

    $handle = fopen('CSVs/cnae_sec/cnaes.csv', 'r');
    if ($handle) {
        while ($data = fgetcsv($handle, 1000, ';')) {
            $checked = $cnae_selfie->checkCANESecundario($data[0], $data[1]);
            if ($checked) {
            } else{
                $cnae_selfie->addCNAESecundario($data[0], $data[1]);
            };
        };    
    } else{
        echo 'erro';
    };
    fclose($handle);

    for ($i=0; $i < count($lista_cnpj); $i++) { 
        $lista = $socio->getSocios($lista_cnpj[$i]);
        for ($i2=0; $i2 < count($lista); $i2++) { 
            $lista_socios[$i2][] = $id_empresas[$i];
            $lista_socios[$i2][] = $lista[$i2]['qualificacao_socio_fk'];
            $lista_socios[$i2][] = $lista[$i2]['n1'];
            $lista_socios[$i2][] = $lista[$i2]['nome_socio'];
            $lista_socios[$i2][] = $lista[$i2]['cnpj_cpf_socio'];
            $lista_socios[$i2][] = $lista[$i2]['cod_qualificacao_socio_fk'];
            $lista_socios[$i2][] = $lista[$i2]['n2'];
            $lista_socios[$i2][] = $lista[$i2]['data_entrada_socio'];
            $lista_socios[$i2][] = $lista[$i2]['nome_representante'];
            $lista_socios[$i2][] = $lista[$i2]['cpf_representante'];
            $lista_socios[$i2][] = $lista[$i2]['cod_qual_representante_fk'];
            $lista_socios[$i2][] = $lista[$i2]['n3'];
            $lista_socios[$i2][] = $lista[$i2]['n4'];
        };
    };

    for ($i=0; $i < count($lista_cnpj); $i++) { 
        $lista = $socio->getSocios($lista_cnpj[$i]);
        for ($i2=0; $i2 < count($lista); $i2++) { 
            $lista_socios[$i2][] = [$id_empresas[$i], $lista[$i2]['qualificacao_socio_fk'], $lista[$i2]['n1'], $lista[$i2]['nome_socio'], $lista[$i2]['cnpj_cpf_socio'],  $lista[$i2]['cod_qualificacao_socio_fk'], $lista[$i2]['n2'], $lista[$i2]['data_entrada_socio'], $lista[$i2]['nome_representante'], $lista[$i2]['cpf_representante'], $lista[$i2]['cod_qual_representante_fk'], $lista[$i2]['n3'], $lista[$i2]['n4']];
        };
    };

    $csv = fopen('socios.csv', 'w');
    foreach ($lista_socios as $key => $value) {
        foreach ($value as $key => $values) {
            fputcsv($csv, $values, ';');
        };        
    };
    fclose($csv);
    rename('socios.csv', 'CSVs/socio/socios.csv');

    $grava_socios = fopen('CSVs/socio/socios.csv', 'r');
    if ($grava_socios) {
        while ($data2 = fgetcsv($grava_socios, 1000, ';')) {
            $data = array();
            for ($i=0; $i < count($data2); $i++) { 
                if ($data2[$i] == '') {
                   $data2[$i] = null;
                };
            };
            $data = $data2;
            $check_socio = new SocioDAO($selfie);
            $checked_socio = $check_socio->checkSocios($data[1], $data[3], $data[4], $data[5], $data[7]);
            if ($checked_socio) {
               $check_socio->updateSocio($checked_socio, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12]);
            } else {
                $check_socio->addSocio($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12]);
            };            
        };
    };

    for ($i=0; $i < $num_de_csv; $i++) {
        unlink('CSVs/agente/agente' . $i . '.csv');
    };

    unlink('CSVs/cnae_sec/cnaes.csv');
    unlink('CSVs/empresa/empresa.csv');
    unlink('CSVs/socio/socios.csv');

    echo 'ok';
    
?>