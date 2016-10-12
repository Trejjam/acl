<?php

namespace Trejjam\Acl\Entity\IdentityHash;

use Nette;
use Kdyby\Doctrine\EntityManager;
use Trejjam;

class IdentityHashFacade
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
