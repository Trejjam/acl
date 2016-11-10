<?php

namespace Trejjam\Acl\Entity\User;

use Doctrine;
use Trejjam\Acl\Entity;
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
	 * @throws UserNotFoundException
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
	 * @throws UserNotFoundException
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

	public function getCountActivated($activated = StatusActivated::STATE_ACTIVATED)
	{
		if ( !in_array($activated, StatusActivated::getValues())) {
			throw new Trejjam\Acl\InvalidArgumentException;
		}

		try {
			$query = $this->em->createQueryBuilder()
							  ->select('COUNT(user.id)')
							  ->from($this->userClassName, 'user')
							  ->andWhere('user.activated = :activated')->setParameter('activated', $activated);

			return $query->getQuery()
						 ->getSingleScalarResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw $e;
		}
	}

	/**
	 * @param bool $showDisabled
	 *
	 * @return User[]
	 */
	public function findAll($showDisabled = TRUE)
	{
		try {
			$query = $this->em->createQueryBuilder()
							  ->select('user')
							  ->from($this->userClassName, 'user');

			if ($showDisabled === FALSE) {
				$query->andWhere('user.status = :status')->setParameter('status', StatusType::STATE_ENABLE);
			}

			$query->orderBy('user.username');

			return $query->getQuery()
						 ->getResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			return [];
		}
	}

	public function mergeCached(User $user)
	{
		$this->em->merge($user);

		return $this;
	}
}
