<?php

namespace Trejjam\Acl\Entity\UserResource;

use Nette;
use Doctrine;
use Kdyby\Doctrine\Types\Enum;
use Trejjam;

class PermissionType extends Enum
{
	const STATUS_ENUM = 'permissionEnum';
	const ALLOW       = 'allow';
	const DENY        = 'deny';

	protected function getValues()
	{
		return [
			self::ALLOW,
			self::DENY,
		];
	}

	public function getSQLDeclaration(array $fieldDeclaration, Doctrine\DBAL\Platforms\AbstractPlatform $platform)
	{
		return sprintf(
			'ENUM(%s)',
			implode(
				', ',
				array_map(function ($arr) {
					return "'" . $arr . "'";
				}, $this->getValues())
			), $this->getName()
		);
	}

	public function convertToDatabaseValue($value, Doctrine\DBAL\Platforms\AbstractPlatform $platform)
	{
		if ( !in_array($value, $this->getValues())) {
			throw new InvalidPermissionTypeException("Invalid status");
		}

		return $value;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return static::STATUS_ENUM;
	}
}
