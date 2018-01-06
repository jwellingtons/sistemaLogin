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
			}else{
				$this->load(
					'Services\PasswordRecovery',
					$this->configs->site->url . $this->configs->baseURI . 'recuperar/redefinir/'
				);

				Recovery::create(array(
					'user_id' => $validar->user->id,
					'token' => $this->passwordrecovery->token,
					'status' => 0
				));

				$message = $this->messages->messages->getByCode('link-enviado', array(
					'message' => array(
						$validar->user->name,
						$this->passwordrecovery->link,
						$this->passwordrecovery->link
					)
				));

				$this->load('Services\Email');

				$envioDoEmail = $this->email->send(
					$validar->user->email,
					$message['subject'],
					$message['message'],
					array(
						'email' => $this->configs->mail->from_mail,
						'remetente' => $this->configs->mail->from
					)
				);

				if ($envioDoEmail === false) {
					$error = $this->messages->getByCode('email-nao-enviado');
				}
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