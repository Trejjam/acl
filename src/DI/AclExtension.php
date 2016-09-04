<?php

namespace Trejjam\Acl\DI;

use Kdyby\Doctrine\DI\IEntityProvider;
use Kdyby\Doctrine\DI\IDatabaseTypeProvider;
use Nette;
use Trejjam;

class AclExtension extends Trejjam\BaseExtension\DI\BaseExtension implements IEntityProvider, IDatabaseTypeProvider
{
	protected $default = [
		'createMissingResource' => TRUE,
	];

	protected $classesDefinition = [
		'user.service'    => Trejjam\Acl\Entity\User\UserService::class,
		'user.repository' => Trejjam\Acl\Entity\User\UserRepository::class,
		'user.facade'     => Trejjam\Acl\Entity\User\UserFacade::class,
		'authenticator'   => Trejjam\Acl\Authenticator::class,
		'authorizator'    => Trejjam\Acl\Authorizator::class,
	];

	/**
	 * Returns associative array of Namespace => mapping definition
	 *
	 * @return array
	 */
	function getEntityMappings()
	{
		return [
			'Trejjam\Acl\Entity' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Entity']),
		];
	}

	/**
	 * Returns array of typeName => typeClass.
	 *
	 * @return array
	 */
	function getDatabaseTypes()
	{
		return [
			'statusEnum'     => Trejjam\Acl\Entity\User\StatusType::class,
			'permissionEnum' => Trejjam\Acl\Entity\UserResource\PermissionType::class,
		];
	}
}
