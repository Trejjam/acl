<?php

namespace Trejjam\Acl\Entity\IdentityHash;

use Nette;
use Trejjam\Acl\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * IdentityHash
 *
 * @ORM\Table(name="users__identity_hash", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class IdentityHash
{
	const HASH_LENGTH = 10;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="hash", type="text", length=65535, nullable=false)
	 */
	private $hash;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ip", type="string", length=50, nullable=false)
	 */
	private $ip;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="action", type="identityStatus", options={"default":IdentityStatus::STATE_NONE}, nullable=false)
	 */
	private $action;

	/**
	 * @var Entity\User\User
	 *
	 * @ORM\ManyToOne(targetEntity=Entity\User\User::class)
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * })
	 */
	private $user;

	public function __construct(Entity\User\User $user, $ip, $hashLength = self::HASH_LENGTH)
	{
		$this->hash = Nette\Utils\Random::generate($hashLength, '0-9A-Z');
		$this->ip = $ip;
		$this->action = IdentityHashStatus::STATE_NONE;
		$this->user = $user;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param string $action
	 *
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action;

		return $this;
	}
}

