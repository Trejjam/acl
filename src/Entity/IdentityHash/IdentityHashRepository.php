<?php

namespace Trejjam\Acl\Entity\IdentityHash;

use Doctrine;
use Kdyby\Doctrine\EntityManager;
use Trejjam;

class IdentityHashRepository
{
	/**
	 * @var EntityManager
	 */
	private $em;

	public function __construct(
		EntityManager $em
	) {
		$this->em = $em;
	}

	/**
	 * @param $id
	 *
	 * @return IdentityHash
	 * @throws Doctrine\ORM\NonUniqueResultException
	 */
	public function getById($id)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('identityHash')
							->from(IdentityHash::class, 'identityHash')
							->andWhere('identityHash.id = :id')->setParameter('id', $id)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new IdentityHashNotFoundException($id, $e);
		}
	}

	/**
	 * @param $hash
	 *
	 * @return IdentityHash
	 * @throws Doctrine\ORM\NonUniqueResultException
	 */
	public function getByHash($hash)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('identityHash')
							->from(IdentityHash::class, 'identityHash')
							->andWhere('identityHash.hash = :hash')->setParameter('hash', $hash)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new IdentityHashNotFoundException($hash, $e);
		}
	}
}
