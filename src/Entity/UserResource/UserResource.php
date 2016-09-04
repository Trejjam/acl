<?php

namespace Trejjam\Acl\Entity\UserResource;

use Nette;
use Doctrine;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`resource`")
 */
class UserResource
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=Entity\UserRole\UserRole::class, cascade={"persist"})
	 * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
	 * @var Entity\UserRole\UserRole
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
	 * @ORM\ManyToOne(targetEntity=Entity\UserRole\UserRole::class, inversedBy="resources")
	 * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
	 * @var Entity\UserRole\UserRole
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
}
