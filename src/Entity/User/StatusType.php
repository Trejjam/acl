<?php

namespace Trejjam\Acl\Entity\User;

use Doctrine;
use Trejjam\Acl\Entity;
use Trejjam;

class StatusType extends Entity\Enum
{
	const ENUM_NAME = 'statusEnum';

	const STATE_ENABLE  = 'enable';
	const STATE_DISABLE = 'disable';
	const STATE_DELETE  = 'delete';

	static public function getValues()
	{
		return [
			self::STATE_ENABLE,
			self::STATE_DISABLE,
			self::STATE_DELETE,
		];
	}
}
