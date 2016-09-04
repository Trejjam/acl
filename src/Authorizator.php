<?php

namespace Trejjam\Acl;

use Nette;
use Trejjam;

class Authorizator implements Nette\Security\IAuthorizator
{

	/**
	 * Performs a role-based authorization.
	 *
	 * @param  string $role
	 * @param  string $resource
	 * @param  string $privilege
	 *
	 * @return bool
	 */
	function isAllowed($role, $resource, $privilege)
	{
		return FALSE;
		// TODO: Implement isAllowed() method.
	}
}
