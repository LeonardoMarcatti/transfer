<?php 
    require_once 'localconection.php'; 
    setlocale(LC_ALL, "pt_BR.utf-8");

    $estado = filter_input(INPUT_POST, 'estados_modal', FILTER_SANITIZE_STRING); 
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cod = filter_input(INPUT_POST, 'cod', FILTER_SANITIZE_NUMBER_INT);
    $municipios = array();

    if ($cod == "") {
        if ($estado != "0" && $nome != "") {
            $sql = "select m.nome as municipio, m.cod_municipio, u.sigla as uf from tb_municipio m join tb_uf u on u.id = m.uf_fk where unaccent(upper(m.nome)) = unaccent(upper(:municipio)) and u.sigla = :uf limit 1";        
        } elseif($estado == "0" && $nome == ""){
            //Todas as cidades do Brasil
            $sql = "select m.nome as municipio, m.cod_municipio, u.sigla as uf from tb_municipio m join tb_uf u on u.id = m.uf_fk";
        } elseif($estado == "0" && $nome != ""){
            $sql = "select distinct(m.nome) as municipio, m.cod_municipio, u.sigla as uf from tb_municipio m join tb_uf u on u.id = m.uf_fk where 
            u.sigla in(select distinct(sigla) from tb_uf where NOT sigla = 'EX') 
            and unaccent(upper(m.nome)) = unaccent(upper(:municipio))";
        } else{
            //Todas as cidades de um estado
            $sql = "select m.nome as municipio, m.cod_municipio, u.sigla as uf from tb_municipio m join tb_uf u on u.id = m.uf_fk where u.sigla = :uf"; 
        };
    } else {
        $sql = "select distinct(m.nome) as municipio, m.cod_municipio, u.sigla as uf from tb_municipio m join tb_uf u on u.id = m.uf_fk where 
        m.cod_municipio = :cod limit 1"; 
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