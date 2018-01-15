<?php
declare(strict_types=1);

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

		'request.service'    => Trejjam\Acl\Entity\Request\RequestService::class,
		'request.repository' => Trejjam\Acl\Entity\Request\RequestRepository::class,

		'identityHash.service'    => Trejjam\Acl\Entity\IdentityHash\IdentityHashService::class,
		'identityHash.repository' => Trejjam\Acl\Entity\IdentityHash\IdentityHashRepository::class,

		'role.service'    => Trejjam\Acl\Entity\Role\RoleService::class,
		'role.repository' => Trejjam\Acl\Entity\Role\RoleRepository::class,
		'role.cache'      => Nette\Caching\Cache::class,

		'authenticator' => Trejjam\Acl\Authenticator::class,
		'authorizator'  => Trejjam\Acl\Authorizator::class,
	];

	public function setConfig(array $config)
	{
		$this->config = $config;

		$this->validateConfig($this->default);

		return $this;
	}

	public function loadConfiguration(bool $validateConfig = TRUE) : void
	{
		parent::loadConfiguration(FALSE);

		$types = $this->getTypes();

		$types['user.service']->setArguments(
			[
				$this->config['user']['className'],
			]
		);
		$types['user.repository']->setArguments(
			[
				$this->config['user']['className'],
			]
		);

		$types['request.service']->setArguments(
			[
				$this->config['request']['timeout'],
			]
		);

		$types['role.repository']->setArguments(
			[
				1 => $this->prefix('@role.cache'),
			]
		);
		$types['role.cache']->setAutowired(FALSE);

		$containerBuilder = $this->getContainerBuilder();
		if ($containerBuilder->hasDefinition('security.userStorage')) {
			$userStorage = $containerBuilder->getDefinition('security.userStorage');
		}
		else {
			$userStorage = $containerBuilder->addDefinition('security.userStorage');
		}

		$userStorage->setFactory(Trejjam\Acl\UserStorage::class);

		if ($containerBuilder->hasDefinition('security.user')) {
			$user = $containerBuilder->getDefinition('security.user');
		}
		else {
			$user = $containerBuilder->addDefinition('security.user');
		}

		$user->setType(Trejjam\Acl\User::class);
	}

	/**
	 * Returns associative array of Namespace => mapping definition
	 *
	 * @return array
	 */
	public function getEntityMappings() : array
	{
		return [
			'Trejjam\Acl\Entity' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Entity',
		];
	}

	/**
	 * Returns array of typeName => typeClass.
	 *
	 * @return array
	 */
	public function getDatabaseTypes() : array
	{
		return [
			'statusEnum'         => Trejjam\Acl\Entity\User\StatusType::class,
			'statusActivated'    => Trejjam\Acl\Entity\User\StatusActivated::class,
			'permissionEnum'     => Trejjam\Acl\Entity\Resource\PermissionType::class,
			'userRequestType'    => $this->config['request']['typeClass'],
			'identityHashStatus' => Trejjam\Acl\Entity\IdentityHash\IdentityHashStatus::class,
		];
	}

	/**
	 * Returns associative array of Interface => Class definition
	 *
	 * @return array
	 */
	public function getTargetEntityMappings() : array
	{
		return [
			Trejjam\Acl\Entity\User\User::class => $this->config['user']['className'],
		];
	}
}
