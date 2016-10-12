<?php

namespace Trejjam\Acl\Entity\IdentityHash;

use Doctrine;
use Trejjam\Acl\Entity;

class IdentityHashStatus extends Entity\Enum
{
	const ENUM_NAME = 'identityHashStatus';

	const STATE_NONE      = 'none';
	const STATE_RELOAD    = 'reload';
	const STATE_LOGOUT    = 'logout';
	const STATE_DESTROYED = 'destroyed';

	static public function getValues()
	{
		return [
			self::STATE_NONE,
			self::STATE_RELOAD,
			self::STATE_LOGOUT,
			self::STATE_DESTROYED,
		];
	}
}