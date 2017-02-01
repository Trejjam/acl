<?php

namespace Trejjam\Acl\Entity\Request;

use Nette;
use Kdyby\Doctrine\EntityManager;
use Trejjam;

/**
 * Class RequestFacade
 *
 * @package Trejjam\Acl\Entity\Request
 *
 * @deprecated use RequestRepository instead
 */
class RequestFacade
{
	/**
	 * @var RequestRepository
	 */
	private $requestRepository;


	/**
	 * RequestFacade constructor.
	 *
	 * @param RequestRepository $requestRepository
	 */
	public function __construct(
		RequestRepository $requestRepository
	) {
		$this->requestRepository = $requestRepository;
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
	 * @deprecated use RequestRepository::createRequest
	 *
	 */
	public function createRequest(Trejjam\Acl\Entity\User\User $user, $type, $extraValue = NULL, $timeout = NULL, $hashLength = Request::HASH_LENGTH)
	{
		return $this->requestRepository->createRequest($user, $type, $extraValue, $timeout, $hashLength);
	}

	/**
	 * @param Request $request
	 *
	 * @return Request
	 * @throws \Exception
	 *
	 * @deprecated use RequestRepository::updateRequest
	 */
	public function updateRequest(Request $request)
	{
		return $this->requestRepository->updateRequest($request);
	}
}
