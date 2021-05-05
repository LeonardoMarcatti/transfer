<?php
    namespace classes;

    class SocioDAO{
        private $pdo;

        public function __construct(\pdo $conection)
        {
            $this->pdo = $conection;
        }

        public function getSocioBrasil()
        {
            $sql = "copy (SELECT s.cnpj_cpf,
            s.qualificacao_socio_fk,
            s.nome_socio,
            s.cnpj_cpf_socio,
            s.cod_qualificacao_socio_fk,
            s.percentual_cap_social,
            s.data_entrada_socio,
            s.cod_pais,
            s.nome_pais,
            s.cpf_representante,
            s.nome_representante,
            s.cod_qual_representante_fk
            FROM tb_socios s
            JOIN tb_empresa e ON e.cnpj_cpf = s.cnpj_cpf) to 
            '/var/www/html/transfer/CSVs/socio/socio.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }

        public function getSocioMunicipios($municipios)
        {
            $sql = "copy (SELECT s.cnpj_cpf,
            s.qualificacao_socio_fk,
            s.nome_socio,
            s.cnpj_cpf_socio,
            s.cod_qualificacao_socio_fk,
            s.percentual_cap_social,
            s.data_entrada_socio,
            s.cod_pais,
            s.nome_pais,
            s.cpf_representante,
            s.nome_representante,
            s.cod_qual_representante_fk
            FROM tb_socios s
            JOIN tb_empresa e ON e.cnpj_cpf = s.cnpj_cpf
            WHERE e.cod_municipio in ($municipios)) to 
            '/var/www/html/transfer/CSVs/socio/socio.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }

        public function getSocioEstado($estado)
        {
            $sql = "copy (SELECT s.cnpj_cpf,
            s.qualificacao_socio_fk,
            s.nome_socio,
            s.cnpj_cpf_socio,
            s.cod_qualificacao_socio_fk,
            s.percentual_cap_social,
            s.data_entrada_socio,
            s.cod_pais,
            s.nome_pais,
            s.cpf_representante,
            s.nome_representante,
            s.cod_qual_representante_fk
            FROM tb_socios s
            JOIN tb_empresa e ON e.cnpj_cpf = s.cnpj_cpf
            WHERE e.uf = '$estado') to 
            '/var/www/html/transfer/CSVs/socio/socio.csv' DELIMITER ';'";
            $select = $this->pdo->prepare($sql);
            $select->execute();
        }
    };

?>