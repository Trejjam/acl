<?php

namespace Trejjam\Acl\Entity\Role;

use Trejjam;

class RoleService
{
	/**
	 *
	 * @param string $name
	 * @param Role   $parent
	 *
	 * @return Role
	 */
	public function createRole($name, Role $parent = NULL)
	{
		return new Role($name, $parent);
	}
}
