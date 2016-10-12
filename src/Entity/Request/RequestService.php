<?php

namespace Trejjam\Acl\Entity\Request;

use Trejjam;

class RequestService
{
	/**
	 * @var
	 */
	private $timeout;

	function __construct($timeout)
	{
		$this->timeout = $timeout;
	}

	/**
	 * @param Trejjam\Acl\Entity\User\User $user
	 * @param string                       $type
	 * @param string|int|bool|null         $extraValue
	 * @param \DateTime|FALSE|NULL         $timeout
	 * @param int                          $hashLength
	 *
	 * @return Request
	 */
	public function createRequest(Trejjam\Acl\Entity\User\User $user, $type, $extraValue = NULL, $timeout = NULL, $hashLength = Request::HASH_LENGTH)
	{
		if (is_null($timeout)) {
			$timeout = new \DateTime;
			$timeout->add(\DateInterval::createFromDateString($this->timeout));
		}

		return new Request($user, $type, $extraValue, $timeout, $hashLength);
	}
}
