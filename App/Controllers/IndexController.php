<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		//Verifica se existe o parametro login na url
		if(isset($_GET['login'])) {
			$this->view->login = $_GET['login'];
		} else {
			$this->view->login = '';
		}

		//Carrega a página index
		$this->render('index');
	}

	public function inscreverse() {

		//Lipar campo de inscrição
		$this->view->usuario = array(
			'nome' => '',
			'email' => '',
			'senha' => ''
		);

		$this->view->erroCadastro = false;

		$this->render('inscreverse');
	}

	public function registrar() {
		
		$usuario = Container::getModel('Usuario');

		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha'])); //Método md5, cripografa a senha

		if($usuario->validarCadastro() && count($usuario->getUsuarioEmail()) == 0) {

			$usuario->salvar();

			$this->render('cadastro');

		} else {

			$this->view->usuario = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha']
			);

			$this->view->erroCadastro = true;

			$this->render('inscreverse');
		}
	} 
}


?>