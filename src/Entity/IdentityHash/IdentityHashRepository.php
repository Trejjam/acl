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
	 * @param int         $id
	 * @param string|null $fetchType
	 *
	 * @return IdentityHash
	 * @throws IdentityHashNotFoundException
	 */
	public function getById($id, $fetchType = NULL)
	{
		try {
			$query = $this->em->createQueryBuilder()
							  ->select('identityHash')
							  ->from(IdentityHash::class, 'identityHash')
							  ->andWhere('identityHash.id = :id')->setParameter('id', $id)
							  ->getQuery();

			if ( !is_null($fetchType)) {
				$query->setFetchMode(IdentityHash::class, 'user', $fetchType);
				$query->setFetchMode(Trejjam\Acl\Entity\User\User::class, 'roles', $fetchType);
			}

			return $query->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new IdentityHashNotFoundException($id, $e);
		}
	}

	/**
	 * @param string      $hash
	 * @param string|null $fetchType
	 *
	 * @return IdentityHash
	 * @throws IdentityHashNotFoundException
	 */
	public function getByHash($hash, $fetchType = NULL)
	{
		try {
			$query = $this->em->createQueryBuilder()
							  ->select('identityHash')
							  ->from(IdentityHash::class, 'identityHash')
							  ->andWhere('identityHash.hash = :hash')->setParameter('hash', $hash)
							  ->getQuery();

			if ( !is_null($fetchType)) {
				$query->setFetchMode(IdentityHash::class, 'user', $fetchType);
				$query->setFetchMode(Trejjam\Acl\Entity\User\User::class, 'roles', $fetchType);
			}

			return $query->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new IdentityHashNotFoundException($hash, $e);
		}
	}

	// =============================================================================
	// write

	/**
	 * @param Trejjam\Acl\Entity\User\User $user
	 * @param string|null                  $ip
	 * @param int                          $hashLength
	 *
	 * @return IdentityHash
	 * @throws \Exception
	 */
	public function createIdentityHash(Trejjam\Acl\Entity\User\User $user, $ip = NULL, $hashLength = IdentityHash::HASH_LENGTH)
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

	public function destroyIdentityHash(IdentityHash $identityHash)
	{
		$identityHash->setAction(IdentityHashStatus::STATE_DESTROYED);

		$this->updateIdentityHash($identityHash);

		return $identityHash;
	}
}
