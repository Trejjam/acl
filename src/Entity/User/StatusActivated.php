<?php

namespace Trejjam\Acl\Entity\User;

use Doctrine;
use Trejjam\Acl\Entity;

class StatusActivated extends Entity\Enum
{
	const ENUM_NAME = 'statusActivated';

	const STATE_ACTIVATED = 'yes';
	const STATE_INACTIVE  = 'no';

	static public function getValues()
	{
		return [
			self::STATE_ACTIVATED,
			self::STATE_INACTIVE,
		];
	}

	public function convertToDatabaseValue($value, Doctrine\DBAL\Platforms\AbstractPlatform $platform)
	{
		return $value ? static::STATE_ACTIVATED : static::STATE_INACTIVE;
	}

	public function convertToPHPValue($value, Doctrine\DBAL\Platforms\AbstractPlatform $platform)
	{
		return $value === static::STATE_ACTIVATED;
	}
}
