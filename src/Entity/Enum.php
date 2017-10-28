<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity;

use Doctrine;
use Kdyby;
use Nette;

abstract class Enum extends Kdyby\Doctrine\Types\Enum
{
	const ENUM_NAME = 'DEFINE ME!';

	static public function getValues() : array
	{
		throw new Nette\NotImplementedException;
	}

	public function getSQLDeclaration(array $fieldDeclaration, Doctrine\DBAL\Platforms\AbstractPlatform $platform) : string
	{
		return sprintf(
			'ENUM(%s)',
			implode(
				', ',
				array_map(function ($arr) {
					return "'" . $arr . "'";
				}, static::getValues())
			), $this->getName()
		);
	}

	public function convertToDatabaseValue($value, Doctrine\DBAL\Platforms\AbstractPlatform $platform)
	{
		if ( !in_array($value, static::getValues(), TRUE)) {
			throw new InvalidStatusException('Invalid status');
		}

		return $value;
	}

	public function getName() : string
	{
		return static::ENUM_NAME;
	}
}
