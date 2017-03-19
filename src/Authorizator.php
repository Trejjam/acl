<?php

namespace Trejjam\Acl;

use Nette;
use Trejjam;

class Authorizator implements Nette\Security\IAuthorizator
{

	/**
	 * Performs a role-based authorization.
	 *
	 * @param Entity\Role\Role         $role
	 * @param null|string|Entity\Resource\Resource $resource
	 * @param null|string                          $privilege
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	function isAllowed($role, $resource = self::ALL, $privilege = self::ALL)
	{
		if ( !$role instanceof Trejjam\Acl\Entity\Role\Role) {
			throw new InvalidArgumentException('Argument must be instance of ' . Trejjam\Acl\Entity\Role\Role::class);
		}

		if ($resource === self::ALL) {
			$resource = 'ALL';
		}
		if ($privilege === self::ALL) {
			$privilege = 'ALL';
		}

		return $role->isAllowed($resource, $privilege);
	}
}
