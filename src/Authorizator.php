<?php

namespace Trejjam\Acl;

use Nette;
use Trejjam;

class Authorizator implements Nette\Security\IAuthorizator
{

	/**
	 * Performs a role-based authorization.
	 *
	 * @param Trejjam\Acl\Entity\Role\Role                $role
	 * @param Trejjam\Acl\Entity\Resource\Resource|string $resource
	 * @param string                                      $privilege
	 *
	 * @return bool
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
