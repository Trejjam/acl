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
	 * @var Entity\Role\Role[]|Doctrine\ORM\PersistentCollection|Doctrine\Common\Collections\ArrayCollection
	 * @ORM\ManyToMany(targetEntity=Entity\Role\Role::class)
	 * @ORM\JoinTable(name="users__user_role",
	 *        joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 *    )
	 */
	protected $roles;

	public function __construct($username)
	{
		$this->username = $username;
		$this->status = StatusType::STATE_ENABLE;
		$this->activated = FALSE;
		$this->roles = new Doctrine\Common\Collections\ArrayCollection;
		$this->createdDate = new Nette\Utils\DateTime;
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
	 * @return Entity\Role\Role[]|Doctrine\Common\Collections\ArrayCollection
	 */
	public function getRoles()
	{
		return $this->roles;
	}
}
