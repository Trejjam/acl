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
	/**
	 * @var IdentityHashService
	 */
	private $identityHashService;

	public function __construct(
		EntityManager $em,
		IdentityHashService $identityHashService
	) {
		$this->em = $em;
		$this->identityHashService = $identityHashService;
	}

	/**
	 * @param int $id
	 *
	 * @return IdentityHash
	 * @throws IdentityHashNotFoundException
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
	 * @param string $hash
	 *
	 * @return IdentityHash
	 * @throws IdentityHashNotFoundException
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

	// =============================================================================
	// write

	/**
	 * @param Trejjam\Acl\Entity\User\User $user
	 * @param string                       $ip
	 * @param int                          $hashLength
	 *
	 * @return IdentityHash
	 * @throws \Exception
	 */
	public function createIdentityHash(Trejjam\Acl\Entity\User\User $user, $ip, $hashLength = IdentityHash::HASH_LENGTH)
	{
		$identityHash = $this->identityHashService->createIdentityHash($user, $ip, $hashLength);

		$this->em->persist($identityHash);
		$this->em->flush();

		return $identityHash;
	}

	public function updateIdentityHash(IdentityHash $identityHash)
	{
		$this->em->beginTransaction();

		try {
			$this->em->flush($identityHash);

			$this->em->commit();
		}
		catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return $identityHash;
	}
}
