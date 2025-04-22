<?php

    namespace App\Controllers;

    use MF\Controller\Action;
    use MF\Model\Container;

    class AuthController extends Action {

        public function autenticar() {
            
            $usuario = Container::getModel('Usuario');
            
            $usuario->__set('email', $_POST['email']);
            $usuario->__set('senha', md5($_POST['senha']));//Usa o MD5 aqui também para comparar hash com hash

            $usuario->autenticar();

            //Verifica se os valores foram setados na autenticação, se não significa que o usuário NÃO está autenticado
            if($usuario->__get('id') != '' && $usuario->__get('nome') != '') {
                
                session_start();
                
                //Seta as credenciais
                $_SESSION['id'] = $usuario->__get('id');
                $_SESSION['nome'] = $usuario->__get('nome');

                header('Location:/timeline');

            } else {

                //Se acontecer algum erro na autenticação o usuário volta para página index, raiz.
                header('Location: /?login=erro'); //Passamos o parametro erro para trabalhar na view
            }
        }

        public function sair() {

            session_start();
            
            session_destroy();

            header('Location: /');
        }
    }

?>