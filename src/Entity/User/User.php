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
 * @ORM\Table(name="`user`")
 */
abstract class User implements Nette\Security\IIdentity
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

	/**
	 * @ORM\Column(type="statusEnum", options={"default":StatusType::ENABLE})
	 * @var string
	 */
	private $status;

	/**
	 * @ORM\Column(type="string", unique=true)
	 * @var string
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string
	 */
	private $password;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	private $createdDate;

	/**
	 * @ORM\ManyToMany(targetEntity=Entity\UserRole\UserRole::class)
	 * @ORM\JoinTable(name="user_role",
	 *        joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
	 *    )
	 */
	private $roles;

	public function __construct($username = NULL)
	{
		$this->username = $username;
		$this->status = StatusType::ENABLE;
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

	public function getUsername()
	{
		return $this->username;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function hashPassword($password)
	{
		$this->password = Nette\Security\Passwords::hash($password);
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	public function flush(EntityManager $entityManager)
	{

	}
}
