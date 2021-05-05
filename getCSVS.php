<?php

    require_once 'localconection.php';
    require_once 'classes/Empresa.php';
    require_once 'classes/CnaeSecundario.php';
    require_once 'classes/Socio.php';
    require_once 'classes/Temp.php';
    require_once 'classes/Selfie.php';
    use classes\SocioDAO;
    use classes\EmpresaDAO;
    use classes\CnaeSecundarioDAO;
    use classes\Temp;
    use classes\SelfieDAO;
    setlocale(LC_ALL, "pt_BR.utf-8");

    $municipios = filter_input(INPUT_POST, 'municipios', FILTER_SANITIZE_STRING);
    $municipios = explode(',', $municipios);
    
    //*Cria os arquivos CSVs de Empresa
    foreach ($municipios as $key => $municipio) {
        $empresa = new EmpresaDAO($bigdata);
        $empresa->getEmpresaMunicipios($municipio);
        $cnae = new CnaeSecundarioDAO($bigdata);
        $cnae->getCNAEMunicipios($municipio);
        $socio = new SocioDAO($bigdata);
        $socio->getSocioMunicipios($municipio);

        //*Cria as tabelas temporárias
        $temp = new Temp($selfie);
        $temp->createTemp();
    
        //*Preenche as tabelas temporarias
        $temp->preencheTabela();

        //*Preenche as tabelas no selfie
        $slf = new SelfieDAO($selfie);
        $bd = new SelfieDAO($bigdata);
        $id = $bd->getMunicipioID($municipio);
        $slf->agenteMunicipios($id);
        $slf->empresaMunicipios();
        $slf->sociosMunicipio();
        $slf->cnaeMunicipios();

        //*Apaga schema temp;
        $temp->dropTemp();
    };
    unlink('CSVs/empresa/empresa.csv');
    unlink('CSVs/socio/socio.csv');
    unlink('CSVs/cnae/cnae.csv');
?>