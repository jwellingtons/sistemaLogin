<?php
class HomeController extends \HXPHP\System\Controller
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

		$this->auth->redirectCheck();

		$this->load(
			'Helpers\Menu',
			$this->request,
			$this->configs,
			$this->auth->getUserRole()
		);

		$user_id = $this->auth->getUserId();

		$this->view->setTitle('HXPHP - Administrativo')
					->setVar('user', User::find($user_id));
	}

	public function bloqueadaAction()
	{
		$this->auth->roleCheck(array(
			'administrator'
		));
	}
}