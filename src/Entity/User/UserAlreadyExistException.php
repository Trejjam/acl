<?php

namespace Trejjam\Acl\Entity\User;

class UserAlreadyExistException extends \LogicException
{
	/**
	 * @var User
	 */
	protected $user;

	public function __construct(User $user)
	{
		parent::__construct();

		$this->user = $user;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
}
