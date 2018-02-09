<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine;
use Kdyby;
use Doctrine\ORM\Mapping as ORM;
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

	public function __construct(string $username)
	{
		$this->createdDate = new \DateTime;
		$this->status = StatusType::STATE_ENABLE;
		$this->activated = FALSE;
		$this->roles = new Doctrine\Common\Collections\ArrayCollection;
		$this->identityHash = new Doctrine\Common\Collections\ArrayCollection;

		$this->setUsername($username);
	}

	public function getUsername() : string
	{
		return $this->username;
	}

	public function getStatus() : string
	{
		return $this->status;
	}

	public function isActivated() : bool
	{
		return $this->activated;
	}

	public function getPassword() : ?string
	{
		return $this->password;
	}

	public function hashPassword(string $password) : self
	{
		$this->password = Nette\Security\Passwords::hash($password);

		return $this;
	}

	public function setActivated(bool $activated = TRUE) : self
	{
		$this->activated = $activated;

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

	public function hasRole(Entity\Role\Role $role) : bool
	{
		return $this->roles->contains($role);
	}

	/**
	 * @return Entity\IdentityHash\IdentityHash[]|Doctrine\Common\Collections\Collection|Doctrine\Common\Collections\Selectable
	 */
	public function getIdentityHash(bool $fetchAll = FALSE)
	{
		if ( !$fetchAll) {
			$criteria = Doctrine\Common\Collections\Criteria::create();
			$criteria->andWhere($criteria::expr()->in('action', [
				Entity\IdentityHash\IdentityHashStatus::STATE_NONE,
				Entity\IdentityHash\IdentityHashStatus::STATE_RELOAD,
			]));

			return $this->identityHash->matching($criteria);
		}

		return $this->identityHash;
	}

	public function getDateCreated() : \DateTime
	{
		return $this->createdDate;
	}

	public function setUsername(string $username) : self
	{
		$this->username = $username;

		return $this;
	}

	public function addRole(Entity\Role\Role $role) : self
	{
		$this->roles->add($role);

		return $this;
	}

	public function removeRole(Entity\Role\Role $role) : self
	{
		$this->roles->removeElement($role);

		return $this;
	}

	public function removeAllRoles() : void
	{
		$this->roles->clear();
	}
}
