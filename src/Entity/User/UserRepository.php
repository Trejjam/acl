<?php

namespace Trejjam\Acl\Entity\User;

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
		$userClassName,
		EntityManager $em
	) {
		$this->userClassName = $userClassName;
		$this->em = $em;
	}

	/**
	 * @param $userId
	 *
	 * @return User
	 * @throws Doctrine\ORM\NonUniqueResultException
	 */
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
	 * @param string      $username
	 * @param string|null $fetchType
	 *
	 * @return User
	 * @throws Doctrine\ORM\NonUniqueResultException
	 */
	public function getByUsername($username, $fetchType = NULL)
	{
		try {
			$query = $this->em->createQueryBuilder()
							  ->select('user')
							  ->from($this->userClassName, 'user')
							  ->andWhere('user.username = :username')->setParameter('username', $username)

							  ->getQuery();

			if ( !is_null($fetchType)) {
				$query->setFetchMode($this->userClassName, 'roles', $fetchType);
			}

			return $query->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new UserNotFoundException($username, $e);
		}
	}

	public function mergeCached(User $user)
	{
		$this->em->merge($user);

		return $this;
	}
}
