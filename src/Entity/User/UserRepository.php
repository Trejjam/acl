<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine;
use Kdyby\Doctrine\EntityManager;
use Trejjam;

class UserRepository
{
	/**
	 * @var string
	 */
	private $userClassName;
	/**
	 * @var EntityManager
	 */
	private $em;

	public function __construct(
		$userClassName = NULL,
		EntityManager $em
	) {
		$this->userClassName = $userClassName ?: User::class;
		$this->em = $em;
	}

	public function getById($userId)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('user')
							->from($this->userClassName, 'user')
							->andWhere('user.id = :id')->setParameter('id', $userId)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new UserNotFoundException($userId, $e);
		}
	}

	/**
	 * @param string $username
	 *
	 * @return User
	 * @throws Doctrine\ORM\NonUniqueResultException
	 */
	public function getByUsername($username)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('user')
							->from($this->userClassName, 'user')
							->andWhere('user.username = :username')->setParameter('username', $username)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new UserNotFoundException($username, $e);
		}
	}
}
