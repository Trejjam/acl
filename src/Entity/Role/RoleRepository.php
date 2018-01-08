<?php
declare(strict_types=1);

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

	public function getById(int $id) : Role
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

	public function getByName(string $name, bool $useCache = FALSE) : Role
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

	public function findByName(array $nameArray)
	{
		return $this->em->createQueryBuilder()
						->select('role')
						->from(Role::class, 'role')
						->andWhere('role.name IN (:roles)')->setParameter('roles', $nameArray)
						->getQuery()
						->getResult();
	}

	/**
	 * @return Role[]
	 */
	public function findRoot() : array
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
			return [];
		}
	}

	/**
	 * @return Role[]
	 */
	public function findAll() : array
	{
		try {
			return $this->em->createQueryBuilder()
							->select('role')
							->from(Role::class, 'role')
							->getQuery()
							->getResult();
		}
		catch (Doctrine\ORM\NoResultException $e) {
			return [];
		}
	}

	// =============================================================================
	// write

	public function createRequest(string $name, Role $parent = NULL) : Role
	{
		$role = $this->roleService->createRole($name, $parent);

		$this->em->persist($role);
		$this->em->flush();

		return $role;
	}

	public function updateRole(Role $role) : Role
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
