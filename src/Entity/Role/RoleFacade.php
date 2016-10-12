<?php

namespace Trejjam\Acl\Entity\Role;

use Kdyby\Doctrine\EntityManager;
use Trejjam;

class RoleFacade
{
	/**
	 * @var EntityManager
	 */
	private $em;
	/**
	 * @var RoleService
	 */
	private $roleService;

	public function __construct(
		EntityManager $em,
		RoleService $roleService
	) {
		$this->em = $em;
		$this->roleService = $roleService;
	}


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
