<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;

/**
 * @ORM\Entity()
 * @ORM\Table(name="`user`")
 */
class User
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $id;

	/**
	 * @ORM\Column(type="enum", columnDefinition="enum(Status::ENABLE, Status::DISABLE, Status::DELETE) DEFAULT Status::ENABLE NOT NULL")
	 */
	private $status;

	/**
	 * @ORM\Column(type="string")
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
}
