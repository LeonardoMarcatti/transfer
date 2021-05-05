<?php
    namespace classes;

    class CnaeSecundarioDAO{
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function getCNAEBrasil()
        {
            $sql = "copy (SELECT cs.cnpj_cpf, cs.cnae_secundario
            FROM tb_cnae_secundario cs
            JOIN tb_empresa e ON e.cnpj_cpf = cs.cnpj_cpf) to 
            '/var/www/html/transfer/CSVs/cnae/cnae.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }

        public function getCNAEMunicipios($municipios)
        {
            $sql = "copy (SELECT cs.cnpj_cpf, cs.cnae_secundario
            FROM tb_cnae_secundario cs
            JOIN tb_empresa e ON e.cnpj_cpf = cs.cnpj_cpf
            WHERE e.cod_municipio in ($municipios)) to 
            '/var/www/html/transfer/CSVs/cnae/cnae.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }

        public function getCNAEEstado($estado)
        {
            $sql = "copy (SELECT cs.cnpj_cpf, cs.cnae_secundario
            FROM tb_cnae_secundario cs
            JOIN tb_empresa e ON e.cnpj_cpf = cs.cnpj_cpf
            WHERE e.uf = '$estado') to 
            '/var/www/html/transfer/CSVs/cnae/cnae.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }


    };    

?>