<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine;
use Kdyby\Doctrine\Types\Enum;
use Trejjam;

class StatusType extends Enum
{
	const STATUS_ENUM = 'statusEnum';
	const ENABLE      = 'enable';
	const DISABLE     = 'disable';
	const DELETE      = 'delete';

	protected function getValues()
	{
		return [
			self::ENABLE,
			self::DISABLE,
			self::DELETE,
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
			throw new InvalidStatusException("Invalid status");
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
