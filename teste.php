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
    $cnae_selfie = new CnaeSecundarioDAO($selfie);
    $cnaes = new CnaeSecundarioDAO($bigdata);
    $cnae_bigdata = new CnaeSecundarioDAO($bigdata);
    $socio = new SocioDAO($bigdata);
    $id_empresas = array();

    class ClassPDO
    {
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function getAgenteEstado($estado)
        {
            $sql = "copy (SELECT * FROM tb_empresa WHERE uf = $estado ORDER BY cnpj_cpf) to 
            '/var/www/html/transfer/CSVs/agente/agente.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }
    }

    $a = new ClassPDO($bigdata);
    $a->getAgenteEstado('AC');    


    

?>