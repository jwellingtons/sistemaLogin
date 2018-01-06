<?php

class RecuperarController extends \HXPHP\System\Controller
{
	public function __construct($configs)
	{
		parent::__construct($configs);

		$this->load(
			'Services\Auth',
			$configs->auth->after_login,
			$configs->auth->after_logout,
			true
		);

		$this->auth->redirectCheck(true);
	}

	public function solicitarAction()
	{
		$this->view->setFile('index');

		//Carrega o modulo de mensagens
		$this->load('Modules\Messages', 'password-recovery');
		$this->messages->setBlock('alerts');

		//Filtra se o campo email enviado no form é um email valido, caso contrario insere null na variavel
		$this->request->setCustomFilters(array(
			'email' => FILTER_VALIDATE_EMAIL
		));

		//Recupera o campo email enviado pelo form
		$email = $this->request->post('email');

		//Cria uma variavel para armazenar as mensagens
		$error = null;

		if (!is_null($email) && $email !== false) {
			$validar = Recovery::validar($email);

			if ($validar->status === false) {
				$error = $this->messages->getByCode($validar->code);
			}
		}

		if (!is_null($error)) {
			//Carrega o erro se existir
			$this->load('Helpers\Alert', $error);
		}else{
			//Caso não exista erro carrega msn de sucesso
			$success = $this->messages->getByCode('link-enviado');

			$this->view->setFile('blank');

			$this->load('Helpers\Alert', $success);
		}
	}

	public function redefinirAction($token)
	{
		# code...
	}

	public function alterarSenhaAction($token)
	{
		# code...
	}
}