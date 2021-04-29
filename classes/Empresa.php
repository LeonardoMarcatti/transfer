<?php
    namespace classes;

    class EmpresaDAO{
        private $pdo;

        public function __construct(\PDO $conection)
        {
            $this->pdo = $conection;
        }

        private function validaCPF($cpf) {

            // Extrai somente os números
            $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
    
            // Verifica se foi informado todos os digitos corretamente
            // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
            if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
                return false;
            };

            // Faz o calculo para validar o CPF
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
            }
            return true;    
        }

        public function getAgenteFK($val)
        {
            $sql = "select id from tb_agente where cpf_cnpj = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            return $select->fetch(\PDO::FETCH_ASSOC)['id'];
        }

        public function getDataFechamento($val)
        {
            $sql = "select situacao_fk, data_situacao from tb_empresa where cnpj_cpf = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $data = $select->fetch(\PDO::FETCH_ASSOC);
            if ($data['situacao_fk'] == 8) {
                return $data['data_situacao'];
            };
            return null;
        }

        public function getInfo($val)
        {
            $sql = "select capital_social, cnae_fiscal, nome_fantasia, cod_nat_juridica, data_situacao from tb_empresa where cnpj_cpf = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            return $select->fetch(\PDO::FETCH_ASSOC);
        }

        public function getRegimeFK($val)
        {
            $cpf = substr($val, -11);
            $checkMEI = $this->validaCPF($cpf);
            if ($checkMEI != false) {
                return $checkMEI;
            } else {
                $sql = "select opc_simples from tb_empresa where cnpj_cpf = :val";
                $select = $this->pdo->prepare($sql);
                $select->bindValue(':val', $val);
                $select->execute();
                $opcao = $select->fetch(\PDO::FETCH_ASSOC)['opc_simples'];
                if ($opcao == 5 or 7) {
                    return '2';
                };
                return '3';
            };
        }

        public function getPorteFK($val)
        {
            $cpf = substr($val, -11);
            $checkMEI = $this->validaCPF($cpf);
            if ($checkMEI != false) {
                return $checkMEI;
            } else {
                $sql = "select porte_empresa from tb_empresa where cnpj_cpf = :val";
                $select = $this->pdo->prepare($sql);
                $select->bindValue(':val', $val);
                $select->execute();
                $opcao = $select->fetch(\PDO::FETCH_ASSOC)['porte_empresa'];
                switch ($opcao) {
                    case '01':
                        return 2;
                    break;
                    case '03':
                        return 3;
                    break;
                    case '05':
                        return 5;
                    break;
                    default:
                        return 0;
                    break;
                };
            };
        }

        public function getSituacaoFK($val)
        {
            $sql = "select porte_empresa from tb_empresa where cnpj_cpf = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $porte = $select->fetch(\PDO::FETCH_ASSOC)['porte_empresa'];
            switch ($porte) {
                case '01':
                    return 2;
                break;
                case '02':
                    return 1;
                break;
                case '03':
                    return 3;
                break;
                case '04':
                    return 4;
                break;
                case '08':
                    return 5;
                break;
                default:
                    null;
                    break;
            }
        }

        public function getSeguimentoFK($val)
        {
            $sql = "select cnae_fiscal from tb_empresa where cnpj_cpf = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $cnae = $select->fetch(\PDO::FETCH_ASSOC)['cnae_fiscal'];
            $cnae_parcial = intval(substr($cnae, 0, 2));
            if ($cnae_parcial == 1 or $cnae_parcial == 2 or $cnae_parcial == 3) {
                return 4;
            } else if($cnae_parcial >= 5 and $cnae_parcial <= 32){
                return 2;
            } else if(($cnae_parcial >= 35 and $cnae_parcial <= 39) ||  
                    ($cnae_parcial >= 49 and $cnae_parcial <= 53) || 
                    $cnae_parcial == 55 || 
                    $cnae_parcial == 56 || 
                    ($cnae_parcial >= 58 and $cnae_parcial <= 66) || 
                    $cnae_parcial == 68 || 
                    ($cnae_parcial >= 69 and $cnae_parcial <= 75) || 
                    ($cnae_parcial >= 77 and $cnae_parcial <= 82) || 
                    ($cnae_parcial >= 84 and $cnae_parcial <= 88) || 
                    ($cnae_parcial >= 90 and $cnae_parcial <= 97) || 
                    $cnae_parcial == 99){
                return 3;
            }
            else if($cnae_parcial == 46 || $cnae_parcial == 47){
              return 1;  
            } elseif($cnae_parcial >= 41 and $cnae_parcial <= 43){
                return 5;
            }else if($cnae_parcial == 45){
                return $this->getTratamentoEspecial($val);
            } else{
                return 6;
            };
        }

        public function getTratamentoEspecial($cnae)
        {
            $cnae_parcial = substr($cnae, 0, 3);
            if ($cnae_parcial == 451 || $cnae_parcial == 453 || $cnae_parcial == 454) {
                return 1;
            } else{
                return 3;
            };
        }

        public function checkEmpresa($val)
        {
            $sql = "select id from tb_empresa where agente_fk = :val";
            $select = $this->pdo->prepare($sql);
            $select->bindValue(':val', $val);
            $select->execute();
            $id = $select->fetch()['id'];
            return $id;
        }

        public function updateEmpresa($id, $ag, $web, $area, $fecha, $lat, $lon, $fat, $qte, $folha, $cap, $cnae, $nome, $reg, $porte, $nat, $ent, $sit, $seg, $datasit)
        {
            $sql = "update tb_empresa set agente_fk = :ag, website = :web, area_construida = :area, data_fechamento = :fecha, latitude = :lat, longitude = :lon, faturamento_anual_bruto = :fat, quantidade_funcionarios = :qte, folha_pagamento_anual = :folha, capital_social = :cap, cnae_primario_fk = :cnae, nome_fantasia = :nome, regime_fk = :reg, porte_fk = :porte, natureza_juridica_fk = :nat, entidade_fk = :ent, situacao_fk = :sit, segmento_fk = :seg, data_situacao = :datasit where id = :id";
            $update = $this->pdo->prepare($sql);
            $update->bindValue(':id', $id);
            $update->bindValue(':ag', $ag);
            $update->bindValue(':web', $web);
            $update->bindValue(':area', $area);
            $update->bindValue(':fecha', $fecha);
            $update->bindValue(':lat', $lat);
            $update->bindValue(':lon', $lon);
            $update->bindValue(':fat', $fat);
            $update->bindValue(':qte', $qte);
            $update->bindValue(':folha', $folha);
            $update->bindValue(':cap', $cap);
            $update->bindValue(':cnae', $cnae);
            $update->bindValue(':nome', $nome);
            $update->bindValue(':reg', $reg);
            $update->bindValue(':porte', $porte);
            $update->bindValue(':nat', $nat);
            $update->bindValue(':ent', $ent);
            $update->bindValue(':sit', $sit);
            $update->bindValue(':seg', $seg);
            $update->bindValue(':datasit', $datasit);
            $update->execute();

        }

        public function addEmpresa($ag, $web, $area, $fecha, $lat, $lon, $fat, $qte, $folha, $cap, $cnae, $nome, $reg, $porte, $nat, $ent, $sit, $seg, $datasit)
        {
            $sql = "insert into tb_empresa (agente_fk, website, area_construida, data_fechamento, latitude, longitude, faturamento_anual_bruto, quantidade_funcionarios, folha_pagamento_anual, capital_social, cnae_primario_fk, nome_fantasia , regime_fk, porte_fk, natureza_juridica_fk, entidade_fk, situacao_fk, segmento_fk, data_situacao) values(:ag, :web, :area, :fecha, :lat, :lon, :fat, :qte, :folha, :cap, :cnae, :nome, :reg, :porte, :nat, :ent, :sit, :seg, :datasit)";
            $add= $this->pdo->prepare($sql);
           $add->bindValue(':ag', $ag);
           $add->bindValue(':web', $web);
           $add->bindValue(':area', $area);
           $add->bindValue(':fecha', $fecha);
           $add->bindValue(':lat', $lat);
           $add->bindValue(':lon', $lon);
           $add->bindValue(':fat', $fat);
           $add->bindValue(':qte', $qte);
           $add->bindValue(':folha', $folha);
           $add->bindValue(':cap', $cap);
           $add->bindValue(':cnae', $cnae);
           $add->bindValue(':nome', $nome);
           $add->bindValue(':reg', $reg);
           $add->bindValue(':porte', $porte);
           $add->bindValue(':nat', $nat);
           $add->bindValue(':ent', $ent);
           $add->bindValue(':sit', $sit);
           $add->bindValue(':seg', $seg);
           $add->bindValue(':datasit', $datasit);
           $add->execute();
        }
    };


?>