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
	/**
	 * @var RoleService
	 */
	private $roleService;

	public function __construct(
		EntityManager $em,
		Nette\Caching\Cache $cache,
		RoleService $roleService
	) {
		$this->em = $em;
		$this->cache = $cache->derive(__CLASS__);
		$this->roleService = $roleService;
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

	// =============================================================================
	// write

	/**
	 *
	 * @param string    $name
	 * @param Role|null $parent
	 *
	 * @return Role
	 * @throws \Exception
	 */
	public function createRequest($name, Role $parent = NULL)
	{
		$role = $this->roleService->createRole($name, $parent);

		$this->em->persist($role);
		$this->em->flush();

		return $role;
	}

	public function updateRole(Role $role)
	{
		$this->em->beginTransaction();

		try {
			$this->em->flush($role);

			$this->em->commit();
		}
		catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return $role;
	}
}
