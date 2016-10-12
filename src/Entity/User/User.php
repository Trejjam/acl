<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\EntityManager;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`users__users`")
 */
abstract class User implements Nette\Security\IIdentity
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id;

	/**
	 * @ORM\Column(type="statusEnum", options={"default":StatusType::STATE_ENABLE})
	 * @var string
	 */
	protected $status;

	/**
	 * @ORM\Column(type="statusActivated", options={"default":StatusActivated::STATE_INACTIVE})
	 * @var string
	 */
	protected $activated;

	/**
	 * @ORM\Column(type="string", unique=true)
	 * @var string
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string
	 */
	protected $password;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
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

	public function __construct($username = NULL)
	{
		$this->username = $username;
		$this->status = StatusType::STATE_ENABLE;
		$this->activated = StatusActivated::STATE_INACTIVE;
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
	 * @param string $activated
	 *
	 * @return $this
	 */
	public function setActivated($activated = StatusActivated::STATE_ACTIVATED)
	{
		$this->activated = $activated;

		return $this;
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 *
	 * @return Trejjam\Authorization\Acl\Role[]|Doctrine\Common\Collections\ArrayCollection
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	public function flush(EntityManager $entityManager)
	{

	}

	public function fetchRoles()
	{
		if ($this->roles instanceof Doctrine\ORM\PersistentCollection || $this->roles instanceof Doctrine\Common\Collections\ArrayCollection) {
			$this->roles = $this->roles->getValues();

			foreach ($this->roles as $role) {
				$role->fetchChild(TRUE);
			}
		}
		else {
			throw new \LogicException('Roles are already fetched');
		}

		return $this;
	}
}
