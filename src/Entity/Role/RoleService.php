<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\Role;

use Trejjam;

class RoleService
{
	public function createRole(string $name, Role $parent = NULL) : Role
	{
		return new Role($name, $parent);
	}
}
