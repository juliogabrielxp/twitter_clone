<?php

    namespace App\Controllers;

    use MF\Controller\Action;
    use MF\Model\Container;

    class AppController extends Action {
  
        public function timeline() {

                //Se NÃO estiverem vazias o usuário está autenticado 
                $this->validaAutenticacao();

                //Recuperação dos tweets
                $tweet = Container::getModel('Tweet');

                $tweet->__set('id_usuario', $_SESSION['id']);

                $tweets = $tweet->getAll();

                $this->view->tweets = $tweets;

                $usuario = Container::getModel('Usuario');

                $usuario->__set('id_usuario', $_SESSION['id']);

                $this->view->info_usuario = $usuario->getInfoUsuario();
                $this->view->total_tweets = $usuario->getTotalTweets();
                $this->view->total_seguindo = $usuario->getTotalSeguindo();
                $this->view->total_seguidores = $usuario->getTotalSeguidores();

                $this->render('timeline');      

        }

        public function tweet() {

            //Se NÃO estiverem vazias o usuário está autenticado 
            $this->validaAutenticacao();

                $tweet = Container::getModel('Tweet');

                $tweet->__set('tweet', $_POST['tweet']);
                $tweet->__set('id_usuario', $_SESSION['id']);

                $tweet->salvar();

                header('Location: /timeline');
                                      
        }

        public function validaAutenticacao() {

            session_start();

            if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {

                header('Location: /?login=erro');
                
            } 
        }

        public function quemSeguir() {

            $this->validaAutenticacao();


            $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] :  '';

            $usuarios = array();

            if($pesquisarPor != "") {

                $usuario = Container::getModel('Usuario');

                $usuario->__set('nome', $pesquisarPor);
                $usuario->__set('id', $_SESSION['id']);
                $usuarios = $usuario->getAll();

            }

            $this->view->usuarios = $usuarios;

            $usuario = Container::getModel('Usuario');

            $usuario->__set('id_usuario', $_SESSION['id']);

            $this->view->info_usuario = $usuario->getInfoUsuario();
            $this->view->total_tweets = $usuario->getTotalTweets();
            $this->view->total_seguindo = $usuario->getTotalSeguindo();
            $this->view->total_seguidores = $usuario->getTotalSeguidores();
            
            $this->render('quemSeguir');
            
        }

        public function acao() {

            $this->validaAutenticacao();

            //Ação
            $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
            //Id_usuario
            $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

            $usuario = Container::getModel('Usuario');
            $usuario->__set('id', $_SESSION['id']);

            if($acao == 'seguir') {

                $usuario->seguirUsuario($id_usuario_seguindo);
            } else {

                $usuario->deixarSeguirUsuario($id_usuario_seguindo);
            }

            header('Location: /quem_seguir');
        }

        public function removerTweet() {

            $this->validaAutenticacao();

            $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
            $id_tweet = isset($_GET['id_tweet']) ? $_GET['id_tweet'] : '';

            $tweet = Container::getModel('Tweet');

            $tweet->__set('id', $id_tweet);
            $tweet->__set('id_usuario', $id_usuario);

            if($acao == 'removerTweet') {
                $tweet->removerTweet();
            }

            header('Location: /timeline');
        }

    }