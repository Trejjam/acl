<?php
declare(strict_types=1);

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
	/**
	 * @var UserService
	 */
	protected $userService;

	public function __construct(
		$userClassName,
		EntityManager $em,
		UserService $userService
	) {
		$this->userClassName = $userClassName;
		$this->em = $em;
		$this->userService = $userService;
	}

	public function getEntityManager(): EntityManager
	{
		return $this->em;
	}

	public function getById(int $id, int $fetchType = NULL) : User
	{
		try {
			$queryBuilder = $this->em->createQueryBuilder()
									 ->select('user')
									 ->from($this->userClassName, 'user')
									 ->andWhere('user.id = :id')
									 ->setParameter('id', $id)
									 ->setMaxResults(1);

			$this->queryBuilderExtender($queryBuilder);

			$query = $queryBuilder->getQuery();

			if ( !is_null($fetchType)) {
				$query->setFetchMode($this->userClassName, 'roles', $fetchType);
			}

			return $query->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new UserNotFoundException($id, $e);
		}
	}

	public function getByUsername(string $username, int $fetchType = NULL) : User
	{
		try {
			$queryBuilder = $this->em->createQueryBuilder()
									 ->select('user')
									 ->from($this->userClassName, 'user')
									 ->andWhere('user.username = :username')->setParameter('username', $username)
									 ->setMaxResults(1);

			$this->queryBuilderExtender($queryBuilder);

			$query = $queryBuilder->getQuery();

			if ( !is_null($fetchType)) {
				$query->setFetchMode($this->userClassName, 'roles', $fetchType);
			}

			return $query->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new UserNotFoundException($username, $e);
		}
	}

	public function getCountActivated(string $activated = StatusActivated::STATE_ACTIVATED) : int
	{
		if ( !in_array($activated, StatusActivated::getValues())) {
			throw new Trejjam\Acl\InvalidArgumentException;
		}

		try {
			$query = $this->em->createQueryBuilder()
							  ->select('COUNT(user.id)')
							  ->from($this->userClassName, 'user')
							  ->andWhere('user.activated = :activated')->setParameter('activated', $activated);

			return intval(
				$query->getQuery()->getSingleScalarResult()
			);
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw $e;
		}
	}

	public function findAll(bool $showDisabled = TRUE) : array
	{
		$queryBuilder = $this->em->createQueryBuilder()
								 ->select('user')
								 ->from($this->userClassName, 'user');

		if ($showDisabled === FALSE) {
			$queryBuilder->andWhere('user.status = :status')->setParameter('status', StatusType::STATE_ENABLE);
		}

		$queryBuilder->orderBy('user.username');

		$this->queryBuilderExtender($queryBuilder);

		return $queryBuilder->getQuery()
							->getResult();
	}

	protected function queryBuilderExtender(Doctrine\ORM\QueryBuilder $queryBuilder)
	{

	}

	// ------------- write -----------------

	public function createUser(string $username, string $password = NULL) : User
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

	public function updateUser(User $user) : User
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

	public function changePassword(User $user, string $password) : User
	{
		$user->hashPassword($password);

		$this->updateUser($user);

		return $user;
	}

	public function setActivated(User $user, bool $activated = TRUE) : User
	{
		$user->setActivated($activated);

		$this->updateUser($user);

		return $user;
	}

	public function addRole(User $user, Entity\Role\Role $role) : User
	{
		$user->addRole($role);

		$this->updateUser($user);

		return $user;
	}

	public function removeRole(User $user, Entity\Role\Role $role) : User
	{
		$user->removeRole($role);

		$this->updateUser($user);

		return $user;
	}
}
