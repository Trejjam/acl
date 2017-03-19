<?php

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

	public function __construct($identityHash, $userId)
	{
		$this->identityHash = $identityHash;
		$this->userId = $userId;
	}

	/**
	 * @return string
	 */
	function getId()
	{
		return $this->identityHash;
	}

	/**
	 * @return string
	 */
	function getIdentityHash()
	{
		return $this->identityHash;
	}

	/**
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return static
	 */
	public function getPreviousSessionIdentity()
	{
		return $this->previousSessionIdentity;
	}

	/**
	 * @param SessionUserIdentity $previousSessionIdentity
	 *
	 * @return static
	 */
	public function setPreviousSessionIdentity(SessionUserIdentity $previousSessionIdentity)
	{
		$this->previousSessionIdentity = $previousSessionIdentity;

		return $this;
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 *
	 * @return array
	 */
	function getRoles()
	{
		throw new Trejjam\Utils\RuntimeException();
	}
}
