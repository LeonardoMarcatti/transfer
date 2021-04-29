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


    class Myclass
    {
        private $id;

        public function setID(int $val)
        {
            $this->id = $val;
        }

        public function getID()
        {
            return $this->id;
        }
    }
    

    class ClassPDO
    {
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function getCodMunicipios()
        {
            $sql = "select cod_municipio from tb_municipio order by cod_municipio";
            $select = $this->pdo->prepare($sql);
            $select->execute();
            $cods = $select->fetchAll(PDO::FETCH_ASSOC);
            return $cods;
        }

        public function getQteEmpresas($val)
        {
            $sql = "select count(situacao_fk) as qte from tb_empresa where cod_municipio = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $qte = $select->fetch()['qte'];
            return $qte;
        }
    }
    /*
    $a = new ClassPDO($bigdata);
    $cod_municipios = $a->getCodMunicipios();

    foreach ($cod_municipios as $key => $value) {
        echo $a->getQteEmpresas($value['cod_municipio']) . "<br>";
    };*/

?>
