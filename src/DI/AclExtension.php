<?php

namespace Trejjam\Acl\DI;

use Kdyby\Doctrine\DI\IEntityProvider;
use Kdyby\Doctrine\DI\IDatabaseTypeProvider;
use Kdyby\Doctrine\DI\ITargetEntityProvider;
use Nette;
use Trejjam;

class AclExtension extends Trejjam\BaseExtension\DI\BaseExtension implements IEntityProvider, IDatabaseTypeProvider, ITargetEntityProvider
{
	protected $default = [
		'createMissingResource' => TRUE,
		'user'                  => [
			'className' => NULL,
		],
		'request'               => [
			'typeClass' => Trejjam\Acl\Entity\Request\RequestType::class,
			'timeout'   => '3 hours',
		],
	];

	protected $classesDefinition = [
		'user.service'    => Trejjam\Acl\Entity\User\UserService::class,
		'user.repository' => Trejjam\Acl\Entity\User\UserRepository::class,
		'user.facade'     => Trejjam\Acl\Entity\User\UserFacade::class,

		'request.service'    => Trejjam\Acl\Entity\Request\RequestService::class,
		'request.repository' => Trejjam\Acl\Entity\Request\RequestRepository::class,
		'request.facade'     => Trejjam\Acl\Entity\Request\RequestFacade::class,

		'identityHash.service'    => Trejjam\Acl\Entity\IdentityHash\IdentityHashService::class,
		'identityHash.repository' => Trejjam\Acl\Entity\IdentityHash\IdentityHashRepository::class,
		'identityHash.facade'     => Trejjam\Acl\Entity\IdentityHash\IdentityHashFacade::class,

		'role.service'    => Trejjam\Acl\Entity\Role\RoleService::class,
		'role.repository' => Trejjam\Acl\Entity\Role\RoleRepository::class,
		'role.facade'     => Trejjam\Acl\Entity\Role\RoleFacade::class,
		'role.cache'      => Nette\Caching\Cache::class,

		'authenticator' => Trejjam\Acl\Authenticator::class,
		'authorizator'  => Trejjam\Acl\Authorizator::class,
	];

	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$config = $this->createConfig();

		$classes = $this->getClasses();

		$classes['user.service']->setArguments(
			[
				$config['user']['className'],
			]
		);
		$classes['user.repository']->setArguments(
			[
				$config['user']['className'],
			]
		);

		$classes['request.service']->setArguments(
			[
				$config['request']['timeout'],
			]
		);

		$classes['role.repository']->setArguments(
			[
				1 => $this->prefix('@role.cache'),
			]
		);
		$classes['role.cache']->setAutowired(FALSE);

		$containerBuilder = $this->getContainerBuilder();
		$containerBuilder->getDefinition('security.userStorage')
						 ->setFactory(Trejjam\Acl\UserStorage::class);

		$containerBuilder->getDefinition('security.user')
						 ->setClass(Trejjam\Acl\User::class);
	}

	/**
	 * Returns associative array of Namespace => mapping definition
	 *
	 * @return array
	 */
	public function getEntityMappings()
	{
		return [
			'Trejjam\Acl\Entity' => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Entity']),
		];
	}

	/**
	 * Returns array of typeName => typeClass.
	 *
	 * @return array
	 * @throws Nette\Utils\AssertionException
	 */
	public function getDatabaseTypes()
	{
		$config = $this->createConfig();

		return [
			'statusEnum'         => Trejjam\Acl\Entity\User\StatusType::class,
			'statusActivated'    => Trejjam\Acl\Entity\User\StatusActivated::class,
			'permissionEnum'     => Trejjam\Acl\Entity\Resource\PermissionType::class,
			'userRequestType'    => $config['request']['typeClass'],
			'identityHashStatus' => Trejjam\Acl\Entity\IdentityHash\IdentityHashStatus::class,
		];
	}

	/**
	 * Returns associative array of Interface => Class definition
	 *
	 * @return array
	 * @throws Nette\Utils\AssertionException
	 */
	public function getTargetEntityMappings()
	{
		$config = $this->createConfig();

		return [
			Trejjam\Acl\Entity\User\User::class => $config['user']['className'],
		];
	}
}
