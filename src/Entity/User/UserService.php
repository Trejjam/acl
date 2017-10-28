<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\User;

use Nette;
use Trejjam;

class UserService
{
	/**
	 * @var string
	 */
	private $userClassName;

	public function __construct(string $userClassName = NULL)
	{
		$this->userClassName = $userClassName ?: User::class;
	}

	public function createUser(string $username) : User
	{
		return new $this->userClassName($username);
	}
}
