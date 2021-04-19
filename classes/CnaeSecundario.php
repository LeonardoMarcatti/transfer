<?php
    namespace classes;

    class CnaeSecundarioDAO{
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function getEmpresaID($val)
        {
            $sql = "select id from tb_empresa where agente_fk = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $id = $select->fetch()['id'];
            return $id;
        }

        public function getCNAES($val)
        {
            $sql = "select cnae_secundario from tb_cnae_secundario where cnpj_cpf = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $values = $select->fetchAll(\PDO::FETCH_ASSOC);
            return $values;
        }

        public function checkCANESecundario($empresa, $cnae)
        {
            $sql = "select * from tb_empresa_cnae_secundario where empresa_fk = :emp and cnae_fk = :cnae";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':emp', $empresa);
            $select->bindValue(':cnae', $cnae);
            $select->execute();
            $check = $select->fetchAll(\PDO::FETCH_ASSOC);

            return ($check)? true : false;
        }

        public function addCNAESecundario($empresa, $cnae)
        {
            $sql = "insert into tb_empresa_cnae_secundario(empresa_fk, cnae_fk) values(:emp, :cnae)";
            $insert = $this->pdo->prepare($sql);
            $insert->bindValue(':emp', $empresa);
            $insert->bindValue(':cnae', $cnae);
            $insert->execute();
        }
    };    

?>