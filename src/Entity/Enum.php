<?php

namespace Trejjam\Acl\Entity;

use Doctrine;
use Kdyby;
use Nette;

abstract class Enum extends Kdyby\Doctrine\Types\Enum
{
	const ENUM_NAME = 'DEFINE ME!';

	static public function getValues()
	{
		throw new Nette\NotImplementedException;
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
		return static::ENUM_NAME;
	}
}