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
	protected $userClassName;
	/**
	 * @var EntityManager
	 */
	protected $em;

	public function __construct(
		$userClassName,
		EntityManager $em,
		UserService $userService
	) {
		$this->userClassName = $userClassName;
		$this->em = $em;
		$this->userService = $userService;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/**
	 * @param $userId
	 *
	 * @return User
	 * @throws UserNotFoundException
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
	 * @throws UserNotFoundException
	 */
	public function getByUsername($username, $fetchType = NULL)
	{
		try {
			$query = $this->em->createQueryBuilder()
							  ->select('user')
							  ->from($this->userClassName, 'user')
							  ->andWhere('user.username = :username')->setParameter('username', $username)
							  ->setMaxResults(1)
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

	/**
	 * @param string $activated
	 *
	 * @return int
	 * @throws Doctrine\ORM\NoResultException
	 * @throws Trejjam\Acl\InvalidArgumentException
	 */
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

	// ------------- write -----------------

	/**
	 * @param string      $username
	 * @param string|null $password
	 *
	 * @return User
	 * @throws \Exception
	 */
	public function createUser($username, $password = NULL)
	{
		try {
			$user = $this->getByUsername($username);

			throw new UserAlreadyExistException($user);
		}
		catch (UserNotFoundException $e) {
			//this is correct behavior
		}

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

		return $user;
	}
}
