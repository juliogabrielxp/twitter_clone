<?php

    namespace App\Models;

    use MF\Model\Model;

    class Usuario extends Model {

        private $id;
        private $nome;
        private $email;
        private  $senha;

        public function __get($atributo) {
            return $this->$atributo;
        }
        

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        //Salvar
        public function salvar() {

            $query = '
            INSERT INTO
                usuarios(nome,email,senha)
            VALUES(:nome, :email, :senha)
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha'));//md5() -> hash 32 cacarteres
            $stmt->execute();

            return  $this;
        }

        //Validar se um cadastro tem os requisitos para ser considerado válido
        public function validarCadastro() {

            $valido  = true;

            if(strlen($this->__get('nome')) < 3) {
                $valido = false;
            }

            if(strlen($this->__get('email')) < 3) {
                $valido = false;
            }

            if(strlen($this->__get('senha')) < 3) {
                $valido = false;
            }

            return $valido;
        }

        //Recuperar usuário por email
        public function getUsuarioEmail() {
            $query = "
            SELECT 
                nome, email
            FROM
                usuarios
            WHERE
                email = :email
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue('email', $this->__get('email'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        //Query para autenticação do usuário
        public function autenticar() {

            $query = '
            SELECT
                id, nome, email
            FROM
                usuarios
            WHERE
                email = :email and senha = :senha' ;
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email',$this->__get('email'));
            $stmt->bindValue(':senha',$this->__get('senha'));
            $stmt->execute();

            //Retorna apenas o primeiro dado
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            //Teste para saber se o usuário está autenticado
            if(!empty($usuario['id']) && !empty($usuario['nome'])) {
                $this->__set('id', $usuario['id']); //Se sim, seta os valores
                $this->__set('nome', $usuario['nome']);
            }

            return $this;
        }

        //Procurar por usuário
        public function getAll() {

            $query = '
            SELECT
                u.id, 
                u.nome, 
                u.email,
                (
                    SELECT
                        count(*)
                    FROM 
                        usuarios_seguidores AS us
                    WHERE
                        us.id_usuario = :id_usuario AND us.id_usuario_seguindo = u.id
                ) AS seguindo_sn
            FROM
                usuarios AS u
            WHERE
                nome LIKE :nome AND id != :id_usuario

            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome','%'.$this->__get('nome').'%');
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function seguirUsuario($id_usuario_seguindo) {

            $query = '
                INSERT INTO 
                    usuarios_seguidores(id_usuario, id_usuario_seguindo)
                VALUES(:id_usuario, :id_usuario_seguindo)
            ';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
            $stmt->execute();

            return true;
        }

        public function deixarSeguirUsuario($id_usuario_seguindo) {

            $query = '
              DELETE FROM
                usuarios_seguidores
              WHERE
                id_usuario = :id_usuario AND id_usuario_seguindo = :id_usuario_seguindo
            ';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
            $stmt->execute();

            return true;
            
        }

        //Informações do usuário
        public function getInfoUsuario() {

            $query = '
             SELECT 
                nome
            FROM
                usuarios
            WHERE
                id = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //Total de tweets
        public function getTotalTweets() {

            $query = '
             SELECT 
                count(*) AS total_tweets
            FROM
                tweets
            WHERE
                id_usuario = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //Total de usuários que estamos seguindo
          //Total de tweets
          public function getTotalSeguindo() {

            $query = '
             SELECT 
                count(*) AS total_seguindo
            FROM
                usuarios_seguidores
            WHERE
                id_usuario = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        //Total de seguidores
        public function getTotalSeguidores() {

            $query = '
             SELECT 
                count(*) AS total_seguidores
            FROM
                usuarios_seguidores
            WHERE
                id_usuario_seguindo = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }

?>