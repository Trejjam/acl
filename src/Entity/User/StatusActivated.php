<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\User;

use Doctrine;
use Trejjam\Acl\Entity;

class StatusActivated extends Entity\Enum
{
	const ENUM_NAME = 'statusActivated';

	const STATE_ACTIVATED = 'yes';
	const STATE_INACTIVE  = 'no';

	static public function getValues() : array
	{
		return [
			self::STATE_ACTIVATED,
			self::STATE_INACTIVE,
		];
	}

	public function convertToDatabaseValue($value, Doctrine\DBAL\Platforms\AbstractPlatform $platform) : string
	{
		return $value ? static::STATE_ACTIVATED : static::STATE_INACTIVE;
	}

	public function convertToPHPValue($value, Doctrine\DBAL\Platforms\AbstractPlatform $platform) : bool
	{
		return $value === static::STATE_ACTIVATED;
	}
}
