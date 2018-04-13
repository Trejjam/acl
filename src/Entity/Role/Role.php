<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\Role;

use Nette;
use Doctrine;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Kdyby;
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
	 * @var Role[]|Collections\Collection
	 *
	 * @ORM\OneToMany(targetEntity=Role::class, mappedBy="parent")
	 */
	private $children;

	/**
	 * @var Role|null
	 *
	 * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="children", cascade={"persist"})
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	private $parent;

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
	private $info;

	/**
	 * @var Entity\Resource\Resource[]|Collections\ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity=Entity\Resource\Resource::class, mappedBy="role")
	 */
	private $resources;

	public function __construct(
		string $name,
		Role $parent = NULL
	) {
		$this->name = $name;
		$this->setParent($parent);
		$this->children = new Collections\ArrayCollection;
		$this->resources = new Collections\ArrayCollection;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getInfo() : string
	{
		return $this->info;
	}

	/**
	 * @return Role[]|Collections\Collection|Collections\Selectable
	 */
	public function getChildren() : Collections\Collection
	{
		return $this->children;
	}

	public function getDepth() : int
	{
		if (is_null($this->parent)) {
			return 0;
		}

		return 1 + $this->parent->getDepth();
	}

	protected function setParent(Role $role = NULL) : self
	{
		$this->parent = $role;

		if ( !is_null($this->parent)) {
			$this->parent->addChild($this);
		}

		return $this;
	}

	/**
	 * @param Role $role
	 *
	 * @internal
	 * @return static
	 */
	public function addChild(Role $role) : self
	{
		$this->children[] = $role;

		return $this;
	}

	/**
	 * @param Entity\Resource\Resource|string|bool|null $resource
	 * @param string|bool|null                          $privilege
	 *
	 * @return bool
	 */
	public function isAllowed($resource, $privilege) : bool
	{
		foreach ($this->resources as $_resource) {
			$resourceName = $_resource->getName();

			if (
				$resourceName === Nette\Security\IAuthorizator::ALL
				|| $resourceName === $resource
			) {
				$resourceAction = $_resource->getAction();

				if (
					$resourceAction === Nette\Security\IAuthorizator::ALL
					|| $resourceAction === $privilege
				) {
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
	public function isChildOf(array $roles) : bool
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
