<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine\ORM\EntityManager;
use Trejjam;

class UserFacade
{
	/**
	 * @var EntityManager
	 */
	private $em;
	/**
	 * @var UserService
	 */
	private $userService;

	public function __construct(
		EntityManager $em,
		UserService $userService
	) {
		$this->em = $em;
		$this->userService = $userService;
	}

	/**
	 * @param string      $username
	 * @param string|null $password
	 *
	 * @return User
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function createUser($username, $password = NULL)
	{
		$user = $this->userService->createUser($username);

		if ( !is_null($password)) {
			$user->hashPassword($password);
		}

		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}
}
