<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Kdyby\Doctrine\EntityManager;
use Trejjam;

/**
 * Class UserFacade
 *
 * @package Trejjam\Acl\Entity\User
 *
 * @deprecated use UserRepository instead
 */
class UserFacade
{
	/**
	 * @var EntityManager
	 */
	private $em;
	/**
	 * @var UserRepository
	 */
	private $userRepository;

	public function __construct(
		EntityManager $em,
		UserRepository $userRepository
	) {
		$this->em = $em;
		$this->userRepository = $userRepository;
	}

	/**
	 * @deprecated use UserRepository::createUser
	 *
	 * @param string      $username
	 * @param string|null $password
	 *
	 * @return User
	 * @throws \Exception
	 */
	public function createUser($username, $password = NULL)
	{
		return $this->userRepository->createUser($username, $password);
	}

	/**
	 * @deprecated use UserRepository::updateUser
	 *
	 * @param User $user
	 *
	 * @return User
	 * @throws \Exception
	 */
	public function updateUser(User $user)
	{
		return $this->userRepository->updateUser($user);
	}

	/**
	 * @deprecated use UserRepository::changePassword
	 *
	 * @param User $user
	 * @param      $password
	 */
	public function changePassword(User $user, $password)
	{
		$this->userRepository->changePassword($user, $password);
	}
}
