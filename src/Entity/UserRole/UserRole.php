<?php

namespace Trejjam\Acl\Entity\UserRole;

use Nette;
use Doctrine;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`role`")
 */
class UserRole
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

	/**
	 * @ORM\OneToMany(targetEntity=UserRole::class, mappedBy="parent")
	 * @var UserRole[]
	 */
	private $children;

	/**
	 * @ORM\ManyToOne(targetEntity=UserRole::class, inversedBy="children", cascade={"persist"})
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 * @var UserRole
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
	 * @ORM\OneToMany(targetEntity=Entity\UserResource\UserResource::class, mappedBy="role")
	 * @var UserRole[]
	 */
	private $resources;

	public function __construct()
	{
		$this->children = new Doctrine\Common\Collections\ArrayCollection;
		$this->resources = new Doctrine\Common\Collections\ArrayCollection;
	}
}
