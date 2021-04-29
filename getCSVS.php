<?php
    //teste
    //teste2
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
    $cnae_selfie = new CnaeSecundarioDAO($selfie);
    $cnaes = new CnaeSecundarioDAO($bigdata);
    $cnae_bigdata = new CnaeSecundarioDAO($bigdata);
    $socio = new SocioDAO($bigdata);
    $empresa2 = new EmpresaDAO($selfie);
    $empresa = new EmpresaDAO($bigdata);
    $id_empresas = array();


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

    //Cria os arquivos CSVs de agente
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

        //*Cria os arquivos CSV do agente
        $nome_arquivo_agente = 'agente' . $i . '.csv';
        $arquivo_agente = fopen($nome_arquivo_agente, 'w');
        $result2 = array();

        foreach ($result as $key => $array) {
            foreach ($array as $key2 => $value) {
                if ($key2 == 'ddd_1' || $key2 == 'ddd_2') {
                    if (strlen($value) == 4) {
                        $value = substr($value, -2);
                    };
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
            fputcsv($arquivo_agente, $value, ';');
        };

        fclose($arquivo[$i]);
        rename($nome_arquivo_agente, 'CSVs/agente/' . $nome_arquivo_agente);

        
        //*Grava os CSVs agente no selfie
        $abre_agente = 'agente' . $i . '.csv';
        $handle = fopen('CSVs/agente/' . $abre_agente, 'r');
        if ($handle) {
            while ($data2 = fgetcsv($handle, 1000, ';')) {
                $data = array();
                for ($i2=0; $i2 < count($data2); $i2++) { 
                    if ($data2[$i2] == '') {
                        // roberto mão santa!
                        $data2[$i2] = null;
                    };
                    array_push($data, $data2[$i2]);
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

        
        //*Cria os CSVs empresa
        $nome_arquivo_empresa = 'empresa' . $i . '.csv';
        $arquivo_empresa = fopen($nome_arquivo_empresa, 'w');
        $CSVfile = 'agente' . $i . '.csv';
        $handle = fopen('CSVs/agente/' . $CSVfile, 'r');
        while ($data = fgetcsv($handle, 1000, ';')) {
            $empresa_csv = array();
            $info = $empresa->getInfo($data[1]);
            $empresa_csv[] = $empresa2->getAgenteFK($data[1]);
            $empresa_csv[] = null;
            $empresa_csv[] = null;
            $empresa_csv[] = $empresa->getDataFechamento($data[1]);
            $empresa_csv[] = null;
            $empresa_csv[] = null;
            $empresa_csv[] = null;
            $empresa_csv[] = null;
            $empresa_csv[] = null;
            $empresa_csv[] = $info['capital_social'];
            $empresa_csv[] = (strlen($info['cnae_fiscal']) == 6) ? 0 . $info['cnae_fiscal'] : $info['cnae_fiscal'];
            $empresa_csv[] = ($info['nome_fantasia']) ? $info['nome_fantasia'] : '-';
            $empresa_csv[] = $empresa->getRegimeFK($data[1]);
            $empresa_csv[] = $empresa->getPorteFK($data[1]);
            $empresa_csv[] = $info['cod_nat_juridica'];
            $empresa_csv[] = null;
            $empresa_csv[] = $empresa->getSituacaoFK($data[1]);
            $empresa_csv[] = $empresa->getSeguimentoFK($data[1]);
            $empresa_csv[] = $info['data_situacao'];
            fputcsv($arquivo_empresa, $empresa_csv, ';');
        };

        fclose($handle);
        fclose($arquivo_empresa);
        rename($nome_arquivo_empresa, 'CSVs/empresa/' . $nome_arquivo_empresa);
        
        
         //*Grava os CSVs empresa no selfie
        $CSVfile = 'empresa' . $i . '.csv';
        $handle = fopen('CSVs/empresa/' . $CSVfile, 'r');
        while ($data2 = fgetcsv($handle, 1000, ';')) {
            $data = array();
            for ($i2=0; $i2 < count($data2); $i2++) { 
                if ($data2[$i2] == '') {
                   $data2[$i2] = null;
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
        fclose($handle);

        
        //*Preenche o CSV de Socios
        $nome_arquivo = 'socios' . $i . '.csv';
        $arquivo = fopen($nome_arquivo, 'w');
        $CSVfile = 'empresa' . $i . '.csv';
        $handle = fopen('CSVs/empresa/' . $CSVfile, 'r');
        while ($data = fgetcsv($handle, 1000, ';')) {
            $array = array();
            $lista_socios = array();
            $info_empresa = $cnae_selfie->getEmpresaInfo($data[0]); # pega id e cnpj
            $lista_socios = $socio->getSocios($info_empresa['cpf_cnpj']);
            foreach ($lista_socios as $key => $value) {
                $array[] = [$info_empresa['id'], $value['qualificacao_socio_fk'], $value['n1'], $value['nome_socio'], $value['cnpj_cpf_socio'], $value['cod_qualificacao_socio_fk'], $value['n2'], $value['data_entrada_socio'], $value['nome_representante'], $value['cpf_representante'], $value['cod_qual_representante_fk'], $value['n3'], $value['n4'],];
            };
            foreach ($array as $key => $value) {
                fputcsv($arquivo, $value, ';');
            };
        };

        fclose($handle);
        fclose($arquivo);
        rename($nome_arquivo, 'CSVs/socio/' . $nome_arquivo);


        
         //*Grava os CSVs socios no selfie
        $CSVfile = 'socios' . $i . '.csv';
        $handle = fopen('CSVs/socio/' . $CSVfile, 'r');
        while ($data2 = fgetcsv($handle, 1000, ';')) {
            $data = array();
            for ($i2=0; $i2 < count($data2); $i2++) { 
                if ($data2[$i2] == '') {
                   $data2[$i2] = null;
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
        fclose($handle);

        //*Cria os CSVs cnae secundario
        $nome_arquivo_cnae = 'cnae' . $i . '.csv';
        $arquivo_cnae = fopen($nome_arquivo_cnae, 'w');
        $CSVfile = 'empresa' . $i . '.csv';
        $handle = fopen('CSVs/empresa/' . $CSVfile, 'r');
        while ($data = fgetcsv($handle, 1000, ';')) {
            $array_cnae = array();
            $lista_cnaes = array();
            $info_empresa = $cnae_selfie->getEmpresaInfo($data[0]); # pega id e cnpj
            $lista_cnaes = $cnae_bigdata->getCNAES($info_empresa['cpf_cnpj']);
            foreach ($lista_cnaes as $key => $value) {
                if (strlen($value['cnae_secundario']) == 6) {
                    $array_cnae[] = [$info_empresa['id'],  0 . $value['cnae_secundario']];
                } else {
                    $array_cnae[] = [$info_empresa['id'], $value['cnae_secundario']];
                };
            };
            foreach ($array_cnae as $key => $value) {
                fputcsv($arquivo_cnae, $value, ';');
            };
        };

        fclose($handle);
        fclose($arquivo_cnae);       
        rename($nome_arquivo_cnae, 'CSVs/cnae/' . $nome_arquivo_cnae);

        //*Grava os cnaes secundarios no selfie
        $CSVfile = 'cnae' . $i . '.csv';
        $handle = fopen('CSVs/cnae/' . $CSVfile, 'r');
        while ($data2 = fgetcsv($handle, 1000, ';')) {
            $data = array();
            for ($i2=0; $i2 < count($data2); $i2++) { 
                if ($data2[$i2] == '') {
                   $data2[$i2] = null;
                };
                $data = $data2;
                $check_cnae = $cnae_selfie->checkCANESecundario($data[0], $data[1]);
                if (!$check_cnae) {
                    $cnae_selfie->addCNAESecundario($data[0], $data[1]);
                };                
            };
        };
        fclose($handle);
        
    };

    for ($i=0; $i < $num_de_csv; $i++) {
        unlink('CSVs/agente/agente' . $i . '.csv');
        unlink('CSVs/empresa/empresa' . $i . '.csv');
        unlink('CSVs/socio/socios' . $i . '.csv');
        unlink('CSVs/cnae/cnae' . $i . '.csv');
    };
?>
