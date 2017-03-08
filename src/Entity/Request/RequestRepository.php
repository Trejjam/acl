<?php

namespace Trejjam\Acl\Entity\Request;

use Doctrine;
use Kdyby\Doctrine\EntityManager;
use Trejjam;

class RequestRepository
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
	 * @param $id
	 *
	 * @return Request
	 * @throws RequestNotFoundException
	 */
	public function getById($id)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('request')
							->from(Request::class, 'request')
							->andWhere('request.id = :id')->setParameter('id', $id)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new RequestNotFoundException($id, $e);
		}
	}

	// =============================================================================
	// write

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
