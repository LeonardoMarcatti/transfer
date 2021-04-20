<?php
    namespace classes;
    class AgenteDAO{
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        public function getAgenteBrasil($limit, $offset)
        {
            $sql = "select 'J' as J, cnpj_cpf, '' as nulo1, cep, logradouro, numero, complemento, m.id as id_municipio,  '' as nulo2, '' as nulo3, '' as nulo4, ddd_1, telefone_1, '' as nulo5, email, '' as nulo6, data_inicio_ativ, razao_social, 
            data_situacao, '' as nulo7, bairro, ddd_2, telefone_2 from tb_empresa e join tb_municipio m on e.cod_municipio = m.cod_municipio offset :offset limit :limit";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':offset', $offset);
            $select->bindValue(':limit', $limit);
            $select->execute(); 
            $result = $select->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }

        public function getAgenteEstado($estado, $limit, $offset)
        {
            $sql = "select 'J' as J, cnpj_cpf, '' as nulo1, cep, logradouro, numero, complemento, '' as nulo2, '' as nulo3, '' as nulo4, ddd_1, telefone_1, '' as nulo5, email, '' as nulo6, data_inicio_ativ, razao_social, data_situacao, '' as nulo7, bairro, ddd_2, telefone_2 from tb_empresa where uf = :uf offset :offset limit :limit";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':uf', $estado);
            $select->bindValue(':offset', $offset);
            $select->bindValue(':limit', $limit);
            $select->execute(); 
            $result = $select->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }

        public function getAgenteMunicipios($municipios, $limit, $offset)
        {
            $sql = "select 'J' as J, cnpj_cpf, '' as nulo1, cep, logradouro, numero, complemento, m.id as id_municipio,  '' as nulo2, '' as nulo3, '' as nulo4, ddd_1, telefone_1, '' as nulo5, email, '' as nulo6, data_inicio_ativ, razao_social, 
            data_situacao, '' as nulo7, bairro, ddd_2, telefone_2 from tb_empresa e join tb_municipio m on e.cod_municipio = m.cod_municipio where e.cod_municipio in ($municipios) order by cnpj_cpf offset :offset limit :limit";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':offset', $offset);
            $select->bindValue(':limit', $limit);
            $select->execute(); 
            $result = $select->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }
        
        public function checkAgente($val)
        {
            $sql = "select id from tb_agente where cpf_cnpj = :val";
            $check = $this->pdo->prepare($sql);
            $check->bindValue(':val', $val);
            $check->execute();
            $id = $check->fetch()['id'];
            return ($id) ? $id : false;
        }

        public function addAgente($fj, $cc, $rg, $cep, $endereco, $numero, $complemento, $municipioFK, $distritoFK, $linhaFK, $bairroFK, $tel1, $celular, $email, $foto, $data_nascimento, $nome, $data_atuaizacao, $status, $bairro, $tel2)
        {
            $sql = "insert into tb_agente(fisica_juridica, cpf_cnpj, rg_insc_estadual, cep, endereco, numero, complemento, municipio_fk, distrito_fk, linha_fk, bairro_fk, telefone, celular, email, foto, data_nascimento_fundacao, nome_razao_social, data_atualizacao, status_atualizacao, bairro, telefone2) values(:fj, :cc, :rg, :cep, :endereco, :numero, :complemento, :municipioFK, :distritoFK, :linhaFK, :bairroFK, :tel1, :celular, :email, :foto, :data_nascimento, :nome, :data_atuaizacao, :status, :bairro, :tel2)";

            $add = $this->pdo->prepare($sql);
            $add->bindValue(':fj', $fj);
            $add->bindValue(':cc', $cc);
            $add->bindValue(':rg', $rg);
            $add->bindValue(':cep', $cep);
            $add->bindValue(':endereco', $endereco);
            $add->bindValue(':numero', $numero);
            $add->bindValue(':complemento', $complemento);
            $add->bindValue(':municipioFK', $municipioFK);
            $add->bindValue(':distritoFK', $distritoFK);
            $add->bindValue(':linhaFK', $linhaFK);
            $add->bindValue(':bairroFK', $bairroFK);
            $add->bindValue(':tel1', $tel1);
            $add->bindValue(':celular', $celular);
            $add->bindValue(':email', $email);
            $add->bindValue(':foto', $foto);
            $add->bindValue(':data_nascimento', $data_nascimento);
            $add->bindValue(':nome', $nome);
            $add->bindValue(':data_atuaizacao', $data_atuaizacao);
            $add->bindValue(':status', $status);
            $add->bindValue(':bairro', $bairro);
            $add->bindValue(':tel2', $tel2);
            $add->execute();
        }

        public function updateAgente($id, $fj, $cc, $rg, $cep, $endereco, $numero, $complemento, $municipioFK, $distritoFK, $linhaFK, $bairroFK, $tel1, $celular, $email, $foto, $data_nascimento, $nome, $data_atuaizacao, $status, $bairro, $tel2)
        {
            $sql = "update tb_agente set fisica_juridica = :fj, cpf_cnpj = :cc, rg_insc_estadual = :reg, cep = :cep, endereco, numero, complemento, municipio_fk, distrito_fk, linha_fk, bairro_fk, telefone, celular, email, foto, data_nascimento_fundacao, nome_razao_social, data_atualizacao, status_atualizacao, bairro, telefone2 = :tel2 where id = :id";

            $update = $this->pdo->prepare($sql);
            $update->bindValue(':id', $id);
            $update->bindValue(':fj', $fj);
            $update->bindValue(':cc', $cc);
            $update->bindValue(':rg', $rg);
            $update->bindValue(':cep', $cep);
            $update->bindValue(':endereco', $endereco);
            $update->bindValue(':numero', $numero);
            $update->bindValue(':complemento', $complemento);
            $update->bindValue(':municipioFK', $municipioFK);
            $update->bindValue(':distritoFK', $distritoFK);
            $update->bindValue(':linhaFK', $linhaFK);
            $update->bindValue(':bairroFK', $bairroFK);
            $update->bindValue(':tel1', $tel1);
            $update->bindValue(':celular', $celular);
            $update->bindValue(':email', $email);
            $update->bindValue(':foto', $foto);
            $update->bindValue(':data_nascimento', $data_nascimento);
            $update->bindValue(':nome', $nome);
            $update->bindValue(':data_atuaizacao', $data_atuaizacao);
            $update->bindValue(':status', $status);
            $update->bindValue(':bairro', $bairro);
            $update->bindValue(':tel2', $tel2);
            $update->execute();
        }
    }; 

?>