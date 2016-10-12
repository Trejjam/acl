<?php

namespace Trejjam\Acl\Entity\Role;

use Nette;
use Doctrine;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`users__roles`")
 * @ORM\Cache(usage="READ_ONLY")
 */
class Role
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

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
		$this->parent = $parent;
		$this->parent->addChild($this);
		$this->children = new Doctrine\Common\Collections\ArrayCollection;
		$this->resources = new Doctrine\Common\Collections\ArrayCollection;
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
	 * @param bool $unlinkParent
	 *
	 * @return $this
	 */
	public function fetchChild($unlinkParent = FALSE)
	{
		if ($this->children instanceof Doctrine\ORM\PersistentCollection || $this->children instanceof Doctrine\Common\Collections\ArrayCollection) {
			if ($unlinkParent && !is_null($this->parent)) {
				$this->parent = TRUE;
			}

			$this->children = $this->children->getValues();

			foreach ($this->children as $child) {
				$child->fetchChild();
			}
		}
		else {
			throw new \LogicException('Child are already fetched');
		}

		if ($this->resources instanceof Doctrine\ORM\PersistentCollection || $this->resources instanceof Doctrine\Common\Collections\ArrayCollection) {
			$this->resources = $this->resources->getValues();
		}

		return $this;
	}

	public function isAllowed($resource, $privilege)
	{
		/** @var Entity\Resource\Resource $_resource */
		foreach ($this->resources as $_resource) {
			$resourceName = $_resource->getName();

			if ($resourceName === 'ALL' || $resourceName === $resource) {
				$resourceAction = $_resource->getAction();

				if ($resourceAction === 'ALL' || $resourceAction === $privilege) {
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
}
