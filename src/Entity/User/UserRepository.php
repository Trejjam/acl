<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine;
use Doctrine\ORM\EntityManager;
use Trejjam;

class UserRepository
{
	/**
	 * @var EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function getById($userId)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('user')
							->from(User::class, 'user')
							->andWhere('user.id = :id')->setParameter('id', $userId)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new UserNotFoundException($userId, $e);
		}
	}

	public function getByUsername($username)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('user')
							->from(User::class, 'user')
							->andWhere('user.username = :username')->setParameter('username', $username)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new UserNotFoundException($username, $e);
		}
	}

	public function changePassword(User $user, $password)
	{
		$user->hashPassword($password);

		$this->em->persist($user);
		$this->em->flush();
	}
}
