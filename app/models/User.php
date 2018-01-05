<?php

/**
*
*/
class User extends \HXPHP\System\Model
{
	static $validates_presence_of = array(
		array(
			'name',
			'message' => 'O nome é um campo obrigatório.'
		),
		array(
			'email',
			'message' => 'O e-mail é um campo obrigatório.'
		),
		array(
			'username',
			'message' => 'O usuário é um campo obrigatório.'
		),
		array(
			'password',
			'message' => 'A senha é um campo obrigatório.'
		)
	);

	static $validates_uniqueness_of = array(
		array(
			'username',
			'message' => 'Já existe um usuário cadastrado!'
		),
		array(
			'email',
			'message' => 'Já existe um e-mail cadastrado!'
		)
	);

	public static function cadastrar($post)
	{
		//Cria uma classe vazia pra armazenar o retorno das validações
		$callbackObj = new \stdClass;
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();

		//Recupera o role_id de user
		$role = Role::find_by_role('User');
		if (is_null($role)) {
			array_push($callbackObj->errors, 'A role user não existe. Contato o administrator');
			return $callbackObj;
		}
		$user_data = array(
			'role_id' => $role->id,
			'status' => 1
		);

		//Cria a senha criptografada usando o HXPHP
		$password = \HXPHP\System\Tools::hashHX($post['password']);

		//Insere a senha criptografada e role no array do post
		$post = array_merge($post, $user_data, $password);

		//Salva os dados no banco de dados
		$cadastrar = self::create($post);

		if ($cadastrar->is_valid()) {
			$callbackObj->user = $cadastrar;
			$callbackObj->status = true;
			return $callbackObj;
		}

		$errors = $cadastrar->errors->get_raw_errors();

		foreach ($errors as $field => $message) {
			array_push($callbackObj->errors, $message[0]);
		}

		return $callbackObj;
	}

	public function login($post)
	{
		//Cria uma classe vazia pra armazenar o retorno das validações
		$callbackObj = new \stdClass;
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->code = array();
		$callbackObj->tentativas_restantes = null;

		$user = self::find_by_username($post['username']);

		if (!is_null($user)) {
			$password = \HXPHP\System\Tools::hashHX($post['password'], $user->salt);

			if ($user->status === 1) {
				if (LoginAttempt::ExistemTentativas($user->id)) {
					if ($password['password'] === $user->password) {
						$callbackObj->user = $user;
						$callbackObj->status = true;

						LoginAttempt::LimparTentativas($user->id);
					}else{
						if (LoginAttempt::TentativasRestantes($user->id) <= 3) {
							$callbackObj->code = 'tentativas-esgotando';
							$callbackObj->tentativas_restantes = LoginAttempt::TentativasRestantes($user->id);
						}else{
							$callbackObj->code = 'dados-incorretos';
						}
						LoginAttempt::RegistrarTentativas($user->id);
					}
				}else{
					$callbackObj->code = 'usuario-bloqueado';

					$user->status = 0;
					$user->save();
				}
			}else{
				$callbackObj->code = 'usuario-bloqueado';
			}
		}else{
			$callbackObj->code = 'usuario-inexistente';
		}

		return $callbackObj;
	}
}