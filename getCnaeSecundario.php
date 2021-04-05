<?php
    require_once 'conections.php'; 
    setlocale(LC_ALL, "pt_BR.utf-8");

    
    $get_Cnpj_From_Tb_empresa = "select cnpj_cpf from tb_empresa where cod_municipio = 7563";
    $lista_Cnpj_Tb_empresa = $bigdata->prepare($get_Cnpj_From_Tb_empresa);
    $lista_Cnpj_Tb_empresa->execute();
    $result_Cnpj_Tb_empresa = $lista_Cnpj_Tb_empresa->fetchAll(PDO::FETCH_ASSOC);


    $lista_id_agente = array();
    foreach ($result_Cnpj_Tb_empresa as $key => $value) {
        $sql_Id_Agente = "select coalesce(id, null) as id from tb_agente where cpf_cnpj = :cnpj";
        $get_id_agente = $selfie->prepare($sql_Id_Agente);
        $get_id_agente->bindValue(':cnjp', $value['cnjp_cpf']);
        $get_id_agente->execute();
        $lista_id_agente[] = $get_id_agente->fetch()['id'];
    };

    var_dump($lista_id_agente);
?>