<?php
    namespace classes;

    class SocioDAO{
        private $pdo;

        public function __construct(\pdo $conection)
        {
            $this->pdo = $conection;
        }

        public function getSocios($val)
        {
            $sql = "select null as \"n1\", qualificacao_socio_fk, nome_socio, cnpj_cpf_socio, cod_qualificacao_socio_fk, null as \"n2\", data_entrada_socio, nome_representante, cpf_representante, cod_qual_representante_fk, null as \"n3\", 
            null as \"n4\" from tb_socios where cnpj_cpf = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $values = $select->fetchAll(\PDO::FETCH_ASSOC);
            return $values;
        }

        public function checkSocios($iden, $nome, $cpf_cnpj, $qua, $data)
        {
            $sql = "select id from tb_socio where cpf_cnpj = :cpf_cnpj and identificador_socio_fk = :iden and nome = :nome and qualificacao_socio_fk = :qua and data_entrada = :data";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':iden', $iden);
            $select->bindValue(':nome', $nome);
            $select->bindValue(':cpf_cnpj', $cpf_cnpj);
            $select->bindValue(':qua', $qua);
            $select->bindValue(':data', $data);
            $select->execute();
            $id = $select->fetch()['id'];
            return $id;
        }

        public function updateSocio($id, $empresa, $iden, $pais = null, $nome, $cpf_cnpj, $qua, $per = null, $data,  $rep_nome, $rep_cpf, $rep_qua, $tel = null, $cel = null)
        {
            $sql = "update tb_socio set empresa_fk = :empresa, identificador_socio_fk = :iden, pais_fk = :pais, nome = :nome, cpf_cnpj = :cpf_cnpj, qualificacao_socio_fk = :qua, percentual_capital_social = :per, data_entrada = :data, representante_nome = :rep_nome, representante_cpf = :rep_cpf, representante_qualificacao_fk = :rep_qua, telefone = :tel, celular = :cel where id = :id";
            $update = $this->pdo->prepare($sql);
            $update->bindValue(':empresa', $empresa);
            $update->bindValue(':iden', $iden);
            $update->bindValue(':pais', $pais);
            $update->bindValue(':nome', $nome);
            $update->bindValue(':cpf_cnpj', $cpf_cnpj);
            $update->bindValue(':qua', $qua);
            $update->bindValue(':per', $per);
            $update->bindValue(':data', $data);
            $update->bindValue(':rep_nome', $rep_nome);
            $update->bindValue(':rep_cpf', $rep_cpf);
            $update->bindValue(':rep_qua', $rep_qua);
            $update->bindValue(':tel', $tel);
            $update->bindValue(':cel', $cel);
            $update->bindValue(':id', $id);
            $update->execute();
        }

        public function addSocio($empresa, $iden, $pais = null, $nome, $cpf_cnpj, $qua, $per = null, $data, $rep_nome, $rep_cpf, $rep_qua, $tel = null, $cel = null)
        {
            $sql = "insert into tb_socio(empresa_fk, identificador_socio_fk, pais_fk, nome, cpf_cnpj, qualificacao_socio_fk, percentual_capital_social, data_entrada, representante_nome, representante_cpf, representante_qualificacao_fk, telefone, celular) values(:empresa, :iden, :pais, :nome, :cpf_cnpj, :qua, :per, :data, :rep_nome, :rep_cpf, :rep_qua, :tel, :cel)";
            $add = $this->pdo->prepare($sql);
            $add->bindValue(':empresa', $empresa);
            $add->bindValue(':iden', $iden);
            $add->bindValue(':pais', $pais);
            $add->bindValue(':nome', $nome);
            $add->bindValue(':cpf_cnpj', $cpf_cnpj);
            $add->bindValue(':qua', $qua);
            $add->bindValue(':per', $per);
            $add->bindValue(':data', $data);
            $add->bindValue(':rep_nome', $rep_nome);
            $add->bindValue(':rep_cpf', $rep_cpf);
            $add->bindValue(':rep_qua', $rep_qua);
            $add->bindValue(':tel', $tel);
            $add->bindValue(':cel', $cel);
            $add->execute();
        }
    };

?>