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
use Codeception\Lib\Driver\MySql;

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
        
        public function getInfo($val)
        {
            $sql = "select capital_social, cnae_fiscal, nome_fantasia, cod_nat_juridica, data_situacao from tb_empresa where cnpj_cpf = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            return $select->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        public function getAgenteFK(Myclass $m)
        {
            $sql = "select cnpj_cpf from tb_empresa where cod_municipio = :val limit 100000";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $m->getID());
            $select->execute();
            return $select->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

        $b = new Myclass();
        $b->setID(6001);
        
        $a = new ClassPDO($bigdata);
        $l = $a->getAgenteFK($b);
        var_dump($l);
?>