<?php

namespace Trejjam\Acl\Entity\Resource;

use Nette;
use Doctrine;
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
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=Entity\Role\Role::class, cascade={"persist"})
	 * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
	 * @var Entity\Role\Role
	 */
	private $parent;

	/**
	 * @ORM\Column(type="string", unique=true)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $action;

	/**
	 * @ORM\ManyToOne(targetEntity=Entity\Role\Role::class, inversedBy="resources")
	 * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
	 * @var Entity\Role\Role
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

	public function getAction()
	{
		if (defined(Nette\Security\IAuthorizator::class . '::' . $this->action)) {
			return constant(Nette\Security\IAuthorizator::class . '::' . $this->action);
		}

		return $this->action;
	}

	/**
	 * @return string
	 */
	public function getPermissionType()
	{
		return $this->permissionType;
	}
}
