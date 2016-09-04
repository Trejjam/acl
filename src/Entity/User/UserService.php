<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Trejjam;

class UserService
{
	/**
	 * @param $username
	 *
	 * @return User
	 */
	public function createUser($username)
	{
		return new User($username);
	}
}
