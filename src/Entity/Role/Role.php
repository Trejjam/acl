<?php

namespace Trejjam\Acl\Entity\Role;

use Nette;
use Doctrine;
use Kdyby;
use Doctrine\ORM\Mapping as ORM;
use Trejjam\Acl;
use Trejjam\Acl\Entity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`users__roles`")
 * @ORM\Cache(usage="READ_ONLY")
 */
class Role
{
	use Kdyby\Doctrine\Entities\Attributes\Identifier;

	/**
	 * @ORM\OneToMany(targetEntity=Role::class, mappedBy="parent")
	 * @var Role[]
	 */
	private $children;

	/**
	 * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="children", cascade={"persist"})
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 * @var Role
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
	private $info;

	/**
	 * @ORM\OneToMany(targetEntity=Entity\Resource\Resource::class, mappedBy="role")
	 * @var Resource[]
	 */
	private $resources;

	public function __construct($name, Role $parent = NULL)
	{
		$this->name = $name;
		$this->setParent($parent);
		$this->children = new Doctrine\Common\Collections\ArrayCollection;
		$this->resources = new Doctrine\Common\Collections\ArrayCollection;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * @return Role[]
	 */
	public function getChildren()
	{
		return $this->children;
	}

	public function getDepth()
	{
		if (is_null($this->parent)) {
			return 0;
		}

		return 1 + $this->parent->getDepth();
	}

	protected function setParent(Role $role = NULL)
	{
		$this->parent = $role;

		if ( !is_null($role)) {
			$this->parent->addChild($this);
		}

		return $this;
	}

	/**
	 * @param Role $role
	 *
	 * @internal
	 * @return $this
	 */
	public function addChild(Role $role)
	{
		$this->children[] = $role;

		return $this;
	}

	/**
	 * @param $resource
	 * @param $privilege
	 *
	 * @return bool
	 */
	public function isAllowed($resource, $privilege)
	{
		/** @var Entity\Resource\Resource $_resource */
		foreach ($this->resources as $_resource) {
			$resourceName = $_resource->getName();

			if ($resourceName === Nette\Security\IAuthorizator::ALL || $resourceName === $resource) {
				$resourceAction = $_resource->getAction();

				if ($resourceAction === Nette\Security\IAuthorizator::ALL || $resourceAction === $privilege) {
					return $_resource->getPermissionType() === Entity\Resource\PermissionType::ALLOW;
				}
			}
		}

		foreach ($this->children as $_role) {
			if ($_role->isAllowed($resource, $privilege)) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @param Role[] $roles
	 *
	 * @return bool
	 */
	public function isChildOf(array $roles)
	{
		if (in_array($this, $roles, TRUE)) {
			return TRUE;
		}
		else if (is_null($this->parent)) {
			return FALSE;
		}
		else {
			return $this->parent->isChildOf($roles);
		}
	}
}
