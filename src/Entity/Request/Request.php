<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\Request;

use Nette;
use Doctrine;
use Kdyby;
use Doctrine\ORM\Mapping as ORM;
use Trejjam;
use Trejjam\Acl\Entity;

/**
 * @ORM\Table(name="users__user_request", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity()
 */
class Request
{
	const HASH_LENGTH = 10;

	use Kdyby\Doctrine\Entities\Attributes\Identifier;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="hash", type="text", length=65535, nullable=false)
	 */
	private $hash;

	/**
	 * @var string
	 */
	private $readableHash;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type", type="userRequestType", nullable=true)
	 */
	private $type = NULL;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="used", type="boolean", options={"default":FALSE}, nullable=false)
	 */
	private $used = FALSE;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="timeout", type="datetime", nullable=true)
	 */
	private $timeout;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="extra_value", type="text", length=65535, nullable=true)
	 */
	private $extraValue;

	/**
	 * @var Entity\User\User
	 *
	 * @ORM\ManyToOne(targetEntity=Entity\User\User::class)
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * })
	 */
	private $user;

	public function __construct(Entity\User\User $user, $type, $extraValue, $timeout, $hashLength = self::HASH_LENGTH)
	{
		$this->user = $user;
		$this->type = $type;
		$this->used = FALSE;
		$this->extraValue = $extraValue;
		$this->timeout = $timeout === FALSE ? NULL : $timeout;
		$this->readableHash = Nette\Utils\Random::generate($hashLength, '0-9A-Z');
		$this->hash = Nette\Security\Passwords::hash($this->readableHash);
	}

	public function getHash() : string
	{
		if (is_null($this->readableHash)) {
			throw new NotReadableHashException;
		}

		return $this->readableHash;
	}

	public function validateHash(
		?Entity\User\User $user,
		string $hash,
		bool $enableUsed = FALSE
	) : bool {
		if ( !$enableUsed && $this->used) {
			throw new AlreadyUsedRequestException;
		}

		if ( !is_null($this->timeout) && $this->timeout < new Nette\Utils\DateTime) {
			throw new ExpiredRequestException;
		}

		if ( !is_null($user) && $user !== $this->user) {
			return FALSE;
		}

		return Nette\Security\Passwords::verify($hash, $this->hash);
	}

	public function getType() : string
	{
		return $this->type;
	}

	public function getExtraValue() : string
	{
		return $this->extraValue;
	}

	public function getUser() : Entity\User\User
	{
		return $this->user;
	}

	public function setUsed(bool $used = TRUE) : self
	{
		$this->used = $used;

		return $this;
	}
}
