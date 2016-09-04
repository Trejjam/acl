<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Trejjam;

class UserService
{
	/**
	 * @var string
	 */
	private $userClassName;

	public function __construct($userClassName = NULL)
	{
		$this->userClassName = $userClassName ?: User::class;
	}

	/**
	 * @param $username
	 *
	 * @return User
	 */
	public function createUser($username)
	{
		return new $this->userClassName($username);
	}
}
