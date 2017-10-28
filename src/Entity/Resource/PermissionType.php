<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\Resource;

use Trejjam\Acl\Entity;

class PermissionType extends Entity\Enum
{
	const ENUM_NAME = 'permissionEnum';
	const ALLOW     = 'allow';
	const DENY      = 'deny';

	static public function getValues() : array
	{
		return [
			self::ALLOW,
			self::DENY,
		];
	}
}
