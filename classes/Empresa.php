<?php
    namespace classes;

    class EmpresaDAO{
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function getEmpresaBrasil()
        {
            $sql = "copy (SELECT * FROM tb_empresa ORDER BY cnpj_cpf) to 
            '/var/www/html/transfer/CSVs/empresa/empresa.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }

        public function getEmpresaMunicipios($municipios)
        {
            $sql = "copy (SELECT * FROM tb_empresa WHERE cod_municipio in ($municipios) ORDER BY cnpj_cpf) to 
            '/var/www/html/transfer/CSVs/empresa/empresa.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }

        public function getEmpresaEstado($estado)
        {
            $sql = "copy (SELECT * FROM tb_empresa WHERE uf = '$estado' ORDER BY cnpj_cpf) to 
            '/var/www/html/transfer/CSVs/empresa/empresa.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }

        
    }; 

?>