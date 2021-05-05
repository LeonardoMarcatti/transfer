<?php
    namespace classes;

    class SelfieDAO{
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function getMunicipioID($val)
        {
            $sql = "select * from tb_municipio where cod_municipio = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $id = $select->fetch(\PDO::FETCH_ASSOC)['id'];
            return $id;
        }

        public function agenteMunicipios($val)
        {
           $sql = "INSERT INTO tb_agente (fisica_juridica, cpf_cnpj, cep, endereco, numero, complemento, municipio_fk, bairro, telefone, telefone2, email, data_nascimento_fundacao, nome_razao_social)
           SELECT
               'J' AS fisica_juridica
               , cnpj_cpf AS cpf_cnpj
               , cep
               , CASE WHEN (tipo_logradouro = 'OUTROS' OR tipo_logradouro = '' OR tipo_logradouro = 'null')
                   THEN logradouro
                   ELSE tipo_logradouro || ' ' || logradouro
               END AS endereco
               , numero
               , complemento
               , :val AS municipio_fk
               , bairro
               , SUBSTRING(ddd_1 FOR 2) || TRIM(telefone_1) AS telefone
               , CASE WHEN (ddd_2 IS NOT NULL) AND (telefone_2 IS NOT NULL)
                   THEN SUBSTRING(ddd_2 FOR 2) || TRIM(telefone_2)
                   ELSE null
               END AS telefone2
               , CASE WHEN email ~ '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$'
                   THEN NULL
                   ELSE email
               END AS email
               , data_inicio_ativ AS data_nascimento_fundacao
               , razao_social AS nome_razao_social
           FROM temp.emp
           ORDER BY cnpj_cpf
           ON CONFLICT (cpf_cnpj) DO UPDATE SET
               fisica_juridica = 'J'
               , cpf_cnpj = EXCLUDED.cpf_cnpj
               , endereco = EXCLUDED.endereco
               , numero = EXCLUDED.numero
               , cep = EXCLUDED.cep
               , complemento = EXCLUDED.complemento
               , municipio_fk = EXCLUDED.municipio_fk
               , bairro = EXCLUDED.bairro
               , telefone = EXCLUDED.telefone
               , telefone2 = EXCLUDED.telefone2
               , email = EXCLUDED.email
               , data_nascimento_fundacao = EXCLUDED.data_nascimento_fundacao
               , nome_razao_social = EXCLUDED.nome_razao_social";

            $insert = $this->pdo->prepare($sql);
            $insert->bindValue(':val', $val);
            $insert->execute();
        }

        public function empresaMunicipios()
        {
            $sql = "INSERT INTO tb_empresa (agente_fk, capital_social, cnae_primario_fk, nome_fantasia, regime_fk, porte_fk, natureza_juridica_fk, situacao_fk, segmento_fk, data_situacao)
            SELECT
                a.id AS agente_fk
                , te.capital_social
                , CASE te.cnae_fiscal
                    WHEN 5812300 THEN '5812301'
                    ELSE LPAD(te.cnae_fiscal::text, 7, '0')
                END AS cnae_primario_fk
                , CASE WHEN (te.nome_fantasia IS NULL OR trim(te.nome_fantasia) = '')
                    THEN te.razao_social
                    ELSE te.nome_fantasia
                END AS nome_fantasia
                , CASE WHEN SUBSTRING(te.razao_social, LENGTH(te.razao_social) - 10, 11) ~ '^[0-9]+$'
                    THEN 1
                    ELSE CASE WHEN te.opc_simples IN ('5', '7')
                        THEN 2
                        ELSE 3
                    END
                END AS regime_fk
                , CASE WHEN SUBSTRING(te.razao_social, LENGTH(te.razao_social) - 10, 11) ~ '^[0-9]+$'
                    THEN 1
                    ELSE CASE porte_empresa
                        WHEN '01' THEN 2
                        WHEN '03' THEN 3
                        WHEN '05' THEN 4
                        WHEN '00' THEN 5
                    END
                END AS porte_fk
                , te.cod_nat_juridica AS natureza_juridica_fk
                , CASE situacao_fk
                    WHEN 1 THEN 2
                    WHEN 2 THEN 1
                    WHEN 3 THEN 3
                    WHEN 4 THEN 4
                    WHEN 8 THEN 5
                END AS situcao_fk
                , CASE
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('01','02','03') THEN 4
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('05','06','07','08','09') THEN 2
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33') THEN 2
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('35') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('36', '37', '38', '39') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('41', '42', '43') THEN 5
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 3) IN ('451') THEN 1
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 3) IN ('452') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 3) IN ('453') THEN 1
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 3) IN ('454') THEN 1
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('46', '47') THEN 1
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('49', '50', '51', '52', '53') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('55', '56') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('58', '59', '60', '61', '62', '63') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('64', '65', '66') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('68') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('69', '70', '71', '72', '73', '74', '75') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('77', '78', '79', '80', '81', '82') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('84') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('85') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('86', '87', '88') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('90', '91', '92', '93') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('94', '95', '96') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('97') THEN 3
                    WHEN SUBSTRING(LPAD(te.cnae_fiscal::text, 7, '0') FOR 2) IN ('99') THEN 3
                    ELSE 6
                END AS segmento_fk
                , te.data_situacao
            FROM temp.emp te
            JOIN tb_agente a ON a.cpf_cnpj = te.cnpj_cpf
            ORDER BY te.cnpj_cpf
            ON CONFLICT (agente_fk) DO UPDATE SET
                agente_fk = EXCLUDED.agente_fk
                , capital_social = EXCLUDED.capital_social
                , cnae_primario_fk = EXCLUDED.cnae_primario_fk
                , nome_fantasia = EXCLUDED.nome_fantasia
                , regime_fk = EXCLUDED.regime_fk
                , porte_fk = EXCLUDED.porte_fk
                , natureza_juridica_fk = EXCLUDED.natureza_juridica_fk
                , situacao_fk = EXCLUDED.situacao_fk
                , segmento_fk = EXCLUDED.segmento_fk
                , data_situacao = EXCLUDED.data_situacao";

                $insert = $this->pdo->prepare($sql);
                $insert->execute();
        }

        public function sociosMunicipio()
        {
            $sql = "INSERT INTO tb_socio (empresa_fk, identificador_socio_fk, nome, cpf_cnpj, qualificacao_socio_fk, data_entrada, representante_nome, representante_qualificacao_fk, representante_cpf)
            SELECT
                e.id AS empresa_fk
                , ts.qualificacao_socio_fk::int AS identificador_socio_fk
                , ts.nome_socio AS nome
                , ts.cnpj_cpf_socio AS cpf_cnpj
                , ts.cod_qualificacao_socio_fk::int AS qualificacao_socio_fk
                , ts.data_entrada_socio AS data_entrada
                , CASE ts.nome_representante
                    WHEN '' THEN NULL
                    WHEN 'CPF INVALIDO' THEN NULL
                    ELSE ts.nome_representante
                END AS representante_nome
                , CASE ts.nome_representante
                    WHEN '' THEN NULL
                    WHEN 'CPF INVALIDO' THEN NULL
                    ELSE cod_qual_representante_fk::int
                END AS representante_qualificacao_fk
                , ts.cpf_representante AS representante_cpf
            FROM temp.s ts
            JOIN tb_agente a ON a.cpf_cnpj = ts.cnpj_cpf
            JOIN tb_empresa e ON e.agente_fk = a.id
            ON CONFLICT (empresa_fk, cpf_cnpj, representante_cpf) DO UPDATE SET
                identificador_socio_fk = EXCLUDED.identificador_socio_fk
                , nome = EXCLUDED.nome
                , cpf_cnpj = EXCLUDED.cpf_cnpj
                , qualificacao_socio_fk = EXCLUDED.qualificacao_socio_fk
                , data_entrada = EXCLUDED.data_entrada
                , representante_nome = EXCLUDED.representante_nome
                , representante_qualificacao_fk = EXCLUDED.representante_qualificacao_fk
                , representante_cpf = EXCLUDED.representante_cpf";

            $insert = $this->pdo->prepare($sql);
            $insert->execute();
        }

        public function cnaeMunicipios()
        {
            $sql = "INSERT INTO tb_empresa_cnae_secundario (empresa_fk, cnae_fk)
            SELECT
                e.id AS empresa_fk
                , CASE cs.cnae_secundario
                    WHEN 4541205 THEN '4541206'
                    WHEN 5812300 THEN '5812301'
                    WHEN 6201500 THEN '6201501'
                    WHEN 4751200 THEN '4751201'
                    WHEN 5822100 THEN '5822101'
                    WHEN 3511500 THEN '3511501'
                    ELSE LPAD(cs.cnae_secundario::text, 7, '0')
                END AS cnae_fk
            FROM temp.cs
            JOIN tb_agente a ON cs.cnpj_cpf = a.cpf_cnpj
            JOIN tb_empresa e ON e.agente_fk = a.id
            WHERE cs.cnae_secundario != 0
            ON CONFLICT DO NOTHING";

            $insert = $this->pdo->prepare($sql);
            $insert->execute();
        }

    };

?>