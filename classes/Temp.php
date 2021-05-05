<?php
    namespace classes;

    class Temp{
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function createTemp()
        {
            $sql = "create schema temp";
            $create = $this->pdo->prepare($sql);
            $create->execute();

            $sql = 'CREATE TABLE temp.emp
            (
                cnpj_cpf character varying(15) COLLATE pg_catalog."default" NOT NULL,
                matriz_filial_fk character varying(1) COLLATE pg_catalog."default",
                razao_social character varying(150) COLLATE pg_catalog."default",
                nome_fantasia character varying(55) COLLATE pg_catalog."default",
                situacao_fk integer,
                data_situacao date,
                motivo_situacao_fk integer,
                nome_cid_exterior character varying(55) COLLATE pg_catalog."default",
                cod_pais character(3) COLLATE pg_catalog."default",
                nome_pais character varying(70) COLLATE pg_catalog."default",
                cod_nat_juridica integer,
                data_inicio_ativ date,
                cnae_fiscal integer,
                tipo_logradouro character varying(20) COLLATE pg_catalog."default",
                logradouro character varying(60) COLLATE pg_catalog."default",
                numero character varying(6) COLLATE pg_catalog."default",
                complemento character varying(156) COLLATE pg_catalog."default",
                bairro character varying(50) COLLATE pg_catalog."default",
                cep character varying(8) COLLATE pg_catalog."default",
                uf character varying(2) COLLATE pg_catalog."default",
                cod_municipio integer,
                municipio character varying(50) COLLATE pg_catalog."default",
                ddd_1 character varying(4) COLLATE pg_catalog."default",
                telefone_1 character varying(8) COLLATE pg_catalog."default",
                ddd_2 character varying(4) COLLATE pg_catalog."default",
                telefone_2 character varying(8) COLLATE pg_catalog."default",
                ddd_fax character varying(4) COLLATE pg_catalog."default",
                num_fax character varying(8) COLLATE pg_catalog."default",
                email character varying(115) COLLATE pg_catalog."default",
                qualificacao_responsavel integer,
                capital_social numeric(14,2),
                porte_empresa character varying(2) COLLATE pg_catalog."default",
                opc_simples character varying(1) COLLATE pg_catalog."default",
                data_opc_simples date,
                data_exc_simples date,
                opc_mei character varying(1) COLLATE pg_catalog."default",
                sit_especial character varying(23) COLLATE pg_catalog."default",
                data_sit_especial date,
                latitude text COLLATE pg_catalog."default",
                longitude text COLLATE pg_catalog."default",
                CONSTRAINT pk_temp_emp_cnpj_cpf PRIMARY KEY (cnpj_cpf)
            )';
            $create = $this->pdo->prepare($sql);
            $create->execute();

            $sql = "ALTER TABLE temp.emp OWNER to postgres";
            $alter = $this->pdo->prepare($sql);
            $alter->execute();

            $sql = 'CREATE TABLE temp.cs
            (
                cnpj_cpf character varying(15) COLLATE pg_catalog."default" NOT NULL,
                cnae_secundario integer
            )';
            $create = $this->pdo->prepare($sql);
            $create->execute();

            $sql = "ALTER TABLE temp.emp OWNER to postgres";
            $alter = $this->pdo->prepare($sql);
            $alter->execute();

            $sql = 'CREATE TABLE temp.s
            (
                cnpj_cpf character varying(16) COLLATE pg_catalog."default" NOT NULL,
                qualificacao_socio_fk character varying(1) COLLATE pg_catalog."default",
                nome_socio character varying(150) COLLATE pg_catalog."default",
                cnpj_cpf_socio character varying(16) COLLATE pg_catalog."default",
                cod_qualificacao_socio_fk character varying(14) COLLATE pg_catalog."default",
                percentual_cap_social numeric(3,2),
                data_entrada_socio date,
                cod_pais character varying(3) COLLATE pg_catalog."default",
                nome_pais character varying(70) COLLATE pg_catalog."default",
                cpf_representante character varying(11) COLLATE pg_catalog."default",
                nome_representante character varying(60) COLLATE pg_catalog."default",
                cod_qual_representante_fk character varying(2) COLLATE pg_catalog."default",
                CONSTRAINT pk_temp_s PRIMARY KEY (cnpj_cpf, cnpj_cpf_socio)
            )';
            $create = $this->pdo->prepare($sql);
            $create->execute();

            $sql = "ALTER TABLE temp.s OWNER to postgres";
            $alter = $this->pdo->prepare($sql);
            $alter->execute();
        }

        public function preencheTabela()
        {
            $sql = "COPY temp.emp FROM '/var/www/html/transfer/CSVs/empresa/empresa.csv' DELIMITER ';'";
            $insert = $this->pdo->prepare($sql);
            $insert->execute();

            $sql = "COPY temp.cs FROM '/var/www/html/transfer/CSVs/cnae/cnae.csv' DELIMITER ';'";
            $insert = $this->pdo->prepare($sql);
            $insert->execute();

            $sql = "COPY temp.s FROM '/var/www/html/transfer/CSVs/socio/socio.csv' DELIMITER ';'";
            $insert = $this->pdo->prepare($sql);
            $insert->execute();
        }

        public function dropTemp()
        {   
            $sql = "drop table temp.emp, temp.cs, temp.s";
            $drop = $this->pdo->prepare($sql);
            $drop->execute();
            
            $sql = "drop schema temp";
            $drop = $this->pdo->prepare($sql);
            $drop->execute();
        }

    };

?>