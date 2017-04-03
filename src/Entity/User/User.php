<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine;
use Kdyby;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\EntityManager;
use Trejjam\Acl;
use Trejjam\Acl\Entity;

/**
 * @ORM\MappedSuperclass
 */
abstract class User implements Nette\Security\IIdentity
{
	use Kdyby\Doctrine\Entities\Attributes\Identifier;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="statusEnum")
	 */
	protected $status;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="statusActivated")
	 */
	protected $activated;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 */
	protected $username;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $password;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_created", type="datetime")
	 */
	protected $createdDate;

	/**
	 * @var Entity\Role\Role[]|Doctrine\Common\Collections\Collection|Doctrine\Common\Collections\Selectable
	 *
	 * @ORM\ManyToMany(targetEntity=Entity\Role\Role::class)
	 * @ORM\JoinTable(name="users__user_role",
	 *        joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 *    )
	 */
	protected $roles;

	/**
	 * @var Entity\IdentityHash\IdentityHash[]|Doctrine\Common\Collections\Collection|Doctrine\Common\Collections\Selectable
	 *
	 * @ORM\OneToMany(targetEntity=Entity\IdentityHash\IdentityHash::class, mappedBy="user")
	 */
	protected $identityHash;

	public function __construct($username)
	{
		$this->createdDate = new \DateTime;
		$this->status = StatusType::STATE_ENABLE;
		$this->activated = FALSE;
		$this->roles = new Doctrine\Common\Collections\ArrayCollection;
		$this->identityHash = new Doctrine\Common\Collections\ArrayCollection;

		$this->setUsername($username);
	}

	/**
	 * Returns the ID of user.
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return bool
	 */
	public function isActivated()
	{
		return $this->activated;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param $password
	 *
	 * @return $this
	 */
	public function hashPassword($password)
	{
		$this->password = Nette\Security\Passwords::hash($password);

		return $this;
	}

	/**
	 * @param bool $activated
	 *
	 * @return $this
	 */
	public function setActivated($activated = TRUE)
	{
		$this->activated = (bool)$activated;

		return $this;
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 *
	 * @return Entity\Role\Role[]|Doctrine\Common\Collections\Collection|Doctrine\Common\Collections\Selectable
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * @return Entity\IdentityHash\IdentityHash[]|Doctrine\Common\Collections\Collection|Doctrine\Common\Collections\Selectable
	 */
	public function getIdentityHash()
	{
		return $this->identityHash;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateCreated()
	{
		return $this->createdDate;
	}

	/**
	 * @param string $username
	 *
	 * @return static
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	public function addRole(Entity\Role\Role $role)
	{
		$this->roles->add($role);

		return $this;
	}

	public function removeRole(Entity\Role\Role $role)
	{
		$this->roles->removeElement($role);

		return $this;
	}
}
