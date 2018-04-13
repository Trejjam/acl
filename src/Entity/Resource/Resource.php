<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\Resource;

use Nette;
use Doctrine;
use Kdyby;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`users__resources`")
 * @ORM\Cache(usage="READ_ONLY")
 */
class Resource
{
	use Kdyby\Doctrine\Entities\Attributes\Identifier;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $action;

	/**
	 * @var Entity\Role\Role
	 *
	 * @ORM\ManyToOne(targetEntity=Entity\Role\Role::class, inversedBy="resources")
	 * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
	 */
	private $role;

	/**
	 * @ORM\Column(type="permissionEnum", options={"default":PermissionType::ALLOW})
	 * @var string
	 */
	private $permissionType;

	public function getName()
	{
		if (defined(Nette\Security\IAuthorizator::class . '::' . $this->name)) {
			return constant(Nette\Security\IAuthorizator::class . '::' . $this->name);
		}

		return $this->name;
	}

	public function getRawName() : string
	{
		return $this->name;
	}

	/**
	 * @return string|bool|null
	 */
	public function getAction()
	{
		if (defined(Nette\Security\IAuthorizator::class . '::' . $this->action)) {
			return constant(Nette\Security\IAuthorizator::class . '::' . $this->action);
		}

		return $this->action;
	}

	public function getRawAction() : string
	{
		return $this->action;
	}

	public function getPermissionType() : string
	{
		return $this->permissionType;
	}
}
