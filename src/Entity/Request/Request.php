<?php

namespace Trejjam\Acl\Entity\Request;

use Trejjam;
use Trejjam\Acl\Entity;
use Nette;

/**
 * @ORM\Table(name="users__request", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity()
 */
class Request
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
	private $isUsed = FALSE;

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
		$this->extraValue = $extraValue;
		$this->timeout = $timeout;
		$this->readableHash = Nette\Utils\Random::generate($hashLength, '0-9A-Z');
		$this->hash = Nette\Security\Passwords::hash($this->readableHash);
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
	 *
	 * @throws NotReadableHashException
	 */
	public function getHash()
	{
		if (is_null($this->readableHash)) {
			throw new NotReadableHashException;
		}

		return $this->readableHash;
	}

	/**
	 * @param Entity\User\User $user
	 * @param string           $hash
	 * @param bool             $enableUsed
	 *
	 * @return bool
	 * @throws InvalidRequestException|AlreadyUsedRequestException
	 * @throws InvalidRequestException|ExpiredRequestException
	 */
	public function validateHash(Entity\User\User $user, $hash, $enableUsed = FALSE)
	{
		if ( !$enableUsed && $this->isUsed) {
			throw new AlreadyUsedRequestException;
		}

		if ($this->timeout < new Nette\Utils\DateTime) {
			throw new ExpiredRequestException;
		}

		return $user === $this->user && Nette\Security\Passwords::verify($hash, $this->hash);
	}

	/**
	 * @return $this
	 */
	public function setUsed()
	{
		$this->isUsed = TRUE;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getExtraValue()
	{
		return $this->extraValue;
	}
}
