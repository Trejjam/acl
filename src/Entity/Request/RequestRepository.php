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

	public function __construct(
		EntityManager $em
	) {
		$this->em = $em;
	}

	/**
	 * @param $id
	 *
	 * @return Request
	 * @throws Doctrine\ORM\NonUniqueResultException
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
}
