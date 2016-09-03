<?php

namespace Trejjam\Acl\DI;

use Kdyby\Doctrine\DI\IEntityProvider;
use Nette;
use Trejjam;

class AclExtension extends Trejjam\BaseExtension\DI\BaseExtension implements IEntityProvider
{
	protected $default = [
		'createMissingResource' => TRUE,
	];

	/**
	 * Returns associative array of Namespace => mapping definition
	 *
	 * @return array
	 */
	function getEntityMappings()
	{
		return [
			implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Entity']),
		];
	}
}
