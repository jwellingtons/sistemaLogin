<?php
class UsuariosController extends \HXPHP\System\Controller
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
		$this->auth->roleCheck(array(
			'administrator'
		));

		$user_id = $this->auth->getUserId();
		$user = User::find($user_id);
		$role = Role::find($user->role_id);

		$this->load(
			'Helpers\Menu',
			$this->request,
			$this->configs,
			$role->role
		);

		$this->view->setTitle('HXPHP - Administrativo')
					->setVars([
						'user' => $user,
						'users' => User::all()
					]);
	}

}