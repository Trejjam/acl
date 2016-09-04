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
	 * @param $username
	 *
	 * @return User
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function createUser($username)
	{
		$user = $this->userService->createUser($username);

		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}
}
