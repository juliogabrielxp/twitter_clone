<?php

    namespace App\Models;

    use MF\Model\Model;

    class Tweet extends Model {

        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

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
                tweets (id_usuario, tweet) 
            VALUES 
                (:id_usuario, :tweet)
            
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));
            $stmt->execute();

            return $this;
        }

        //Recuperar
        public function getAll() {

            //Uso de alias para filtar os dados
            //Uso de subconsultas com IN()
            $query = "
            SELECT 
                t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
            FROM
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            WHERE
                t.id_usuario = :id_usuario
                    OR
                t.id_usuario IN ( 
                    SELECT 
                        id_usuario_seguindo
                    FROM
                        usuarios_seguidores
                    WHERE
                        id_usuario = :id_usuario
                )
            ORDER BY
                t.data desc
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function removerTweet() {
            
            $query = '
                DELETE FROM 
                    tweets
                WHERE 
                    id = :id AND id_usuario = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return true;
        }
    }

?>