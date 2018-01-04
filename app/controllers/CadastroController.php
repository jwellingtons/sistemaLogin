<?php

class CadastroController extends \HXPHP\System\Controller
{
	public function cadastrarAction()
	{
		//Redireciona para uma view
		$this->view->setFile('index');

		//Filtra/valida dados do form
		$this->request->setCustomFilters(array(
			'email' => FILTER_VALIDATE_EMAIL
		));

		//Chama o model
		$cadastrarUsuario = User::cadastrar($this->request->post());
	}
}