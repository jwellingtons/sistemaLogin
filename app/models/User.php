<?php

/**
*
*/
class User extends \HXPHP\System\Model
{
	public function cadastrar($post)
	{
		//Recupera o role_id de user
		$role = Role::find_by_role('User');

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
		return self::create($post);
	}
}