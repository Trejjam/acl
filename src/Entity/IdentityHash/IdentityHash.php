<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\IdentityHash;

use Nette;
use Doctrine;
use Kdyby;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * @ORM\Table(name="users__identity_hash")
 * @ORM\Entity
 */
class IdentityHash
{
	use Kdyby\Doctrine\Entities\Attributes\Identifier;

	const HASH_LENGTH = 30;

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

	public function __construct(
		Entity\User\User $user,
		string $ip,
		int $hashLength = self::HASH_LENGTH
	) {
		$this->hash = Nette\Utils\Random::generate($hashLength, '0-9A-Z');
		$this->ip = $ip;
		$this->action = IdentityHashStatus::STATE_NONE;
		$this->user = $user;
	}

	public function getHash() : string
	{
		return $this->hash;
	}

	public function getIp() : string
	{
		return $this->ip;
	}

	public function getAction() : string
	{
		return $this->action;
	}

	public function setAction(string $action) : self
	{
		$this->action = $action;

		return $this;
	}

	public function getUser() : Entity\User\User
	{
		return $this->user;
	}
}

