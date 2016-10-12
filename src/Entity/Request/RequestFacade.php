<?php

namespace Trejjam\Acl\Entity\Request;

use Nette;
use Kdyby\Doctrine\EntityManager;
use Trejjam;

class RequestFacade
{
	/**
	 * @var EntityManager
	 */
	private $em;
	/**
	 * @var RequestService
	 */
	private $requestService;

	public function __construct(
		EntityManager $em,
		RequestService $requestService
	) {
		$this->em = $em;
		$this->requestService = $requestService;
	}


	/**
	 * @param Trejjam\Acl\Entity\User\User $user
	 * @param string                       $type
	 * @param string|int|bool|null         $extraValue
	 * @param \DateTime|FALSE|NULL         $timeout
	 * @param int                          $hashLength
	 *
	 * @return Request
	 * @throws \Exception
	 *
	 */
	public function createRequest(Trejjam\Acl\Entity\User\User $user, $type, $extraValue = NULL, $timeout = NULL, $hashLength = Request::HASH_LENGTH)
	{
		$request = $this->requestService->createRequest($user, $type, $extraValue, $timeout, $hashLength);

		$this->em->persist($request);
		$this->em->flush();

		return $request;
	}

	public function updateRequest(Request $request)
	{
		$this->em->beginTransaction();

		try {
			$this->em->flush($request);

			$this->em->commit();
		}
		catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return $request;
	}
}
