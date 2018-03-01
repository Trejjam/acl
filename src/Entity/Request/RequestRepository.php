<?php
declare(strict_types=1);

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

	public function getById(int $id) : Request
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

	public function createRequest(
		Trejjam\Acl\Entity\User\User $user,
		string $type,
		string $extraValue = NULL,
		$timeout = NULL,
		int $hashLength = Request::HASH_LENGTH
	) : Request {
		$request = $this->requestService->createRequest($user, $type, $extraValue, $timeout, $hashLength);

		$this->em->persist($request);
		$this->em->flush();

		return $request;
	}

	public function updateRequest(Request $request) : Request
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
