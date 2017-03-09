<?php

namespace Trejjam\Acl\Entity\IdentityHash;

use Nette;
use Doctrine;
use Kdyby;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * IdentityHash
 *
 * @ORM\Table(name="users__identity_hash")
 * @ORM\Entity
 */
class IdentityHash
{
	use Kdyby\Doctrine\Entities\Attributes\Identifier;

	const HASH_LENGTH = 10;

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
	 * @ORM\Column(name="action", type="identityHashStatus", nullable=false)
	 */
	private $action;

	/**
	 * @var Entity\User\User
	 *
	 * @ORM\ManyToOne(targetEntity=Entity\User\User::class, inversedBy="identityHash")
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

