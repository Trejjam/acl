<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Kdyby\Doctrine\EntityManager;
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
	 * @throws \Exception
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

	public function updateUser(User $user)
	{
		$this->em->beginTransaction();

		try {
			$user->flush($this->em);
			$this->em->flush($user);

			$this->em->commit();
		}
		catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return $user;
	}

	public function changePassword(User $user, $password)
	{
		$user->hashPassword($password);

		$this->updateUser($user);
	}
}
