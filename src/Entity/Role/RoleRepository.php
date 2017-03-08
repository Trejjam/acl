<?php

namespace Trejjam\Acl\Entity\Role;

use Doctrine;
use Kdyby\Doctrine\EntityManager;
use Trejjam;
use Nette;

class RoleRepository
{
	/**
	 * @var EntityManager
	 */
	private $em;
	/**
	 * @var Nette\Caching\Cache
	 */
	private $cache;

	public function __construct(
		EntityManager $em,
		Nette\Caching\Cache $cache
	) {
		$this->em = $em;
		$this->cache = $cache->derive(__CLASS__);
	}

	/**
	 * @param $id
	 *
	 * @return Role
	 * @throws RoleNotFoundException
	 */
	public function getById($id)
	{
		try {
			return $this->em->createQueryBuilder()
							->select('role')
							->from(Role::class, 'role')
							->andWhere('role.id = :id')->setParameter('id', $id)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new RoleNotFoundException($id, $e);
		}
	}

	/**
	 * @param string $name
	 * @param bool   $useCache
	 *
	 * @return Role
	 * @throws RoleNotFoundException
	 */
	public function getByName($name, $useCache = FALSE)
	{
		//It is probably bad idea, need check in bigger project
		if (FALSE && $useCache) {
			return $this->cache->load($name, function (&$options = []) use ($name) {
				$options [Nette\Caching\Cache::EXPIRE] = '+ 20 minutes';

				return $this->getByName($name);
			});
		}

		try {
			return $this->em->createQueryBuilder()
							->select('role')
							->from(Role::class, 'role')
							->andWhere('role.name = :name')->setParameter('name', $name)
							->getQuery()
							->getSingleResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw new RoleNotFoundException($name, $e);
		}
	}

	public function findRoot()
	{
		try {
			return $this->em->createQueryBuilder()
							->select('role')
							->from(Role::class, 'role')
							->andWhere('role.parent IS NULL')
							->getQuery()
							->getResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			throw $e;
		}
	}
}
