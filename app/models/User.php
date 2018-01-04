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
			array('username', 'email'),
			'message' => 'Já existe um usuário e/ou email cadastrado!'
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

		//Insere os dados recuperado do role no array do post
		$post = array_merge($post, array(
			'role_id' => $role->id,
			'status' => 1
		));

		//Cria a senha criptografada usando o HXPHP
		$password = \HXPHP\System\Tools::hashHX($post['password']);

		//Insere a senha criptografada no array do post
		$post = array_merge($post, $password);

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
}