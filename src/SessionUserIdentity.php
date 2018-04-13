<?php
declare(strict_types=1);

namespace Trejjam\Acl;

use Nette;
use Trejjam;

class SessionUserIdentity implements Nette\Security\IIdentity
{
	/**
	 * @var string
	 */
	private $identityHash;
	/**
	 * @var int
	 */
	private $userId;
	/**
	 * @var static
	 */
	private $previousSessionIdentity;

	public function __construct(
		string $identityHash,
		int $userId
	) {
		$this->identityHash = $identityHash;
		$this->userId = $userId;
	}

	function getId() : string
	{
		return $this->identityHash;
	}

	function getIdentityHash() : string
	{
		return $this->identityHash;
	}

	public function getUserId() : int
	{
		return $this->userId;
	}

	public function getPreviousSessionIdentity() : ?self
	{
		return $this->previousSessionIdentity;
	}

	public function setPreviousSessionIdentity(SessionUserIdentity $previousSessionIdentity) : self
	{
		$this->previousSessionIdentity = $previousSessionIdentity;

		return $this;
	}

	public function getRoles() : array
	{
		throw new Trejjam\Acl\UnsupportedMethodException;
	}
}
