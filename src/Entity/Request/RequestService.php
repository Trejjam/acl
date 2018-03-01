<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\Request;

use Trejjam;

class RequestService
{
	/**
	 * @var string
	 */
	private $timeout;

	function __construct(string $timeout)
	{
		$this->timeout = $timeout;
	}

	/**
	 * @param Trejjam\Acl\Entity\User\User  $user
	 * @param string                        $type
	 * @param string|null                   $extraValue
	 * @param \DateTimeImmutable|FALSE|NULL $timeout
	 * @param int                           $hashLength
	 *
	 * @return Request
	 */
	public function createRequest(
		Trejjam\Acl\Entity\User\User $user,
		string $type,
		$extraValue = NULL,
		$timeout = NULL,
		$hashLength = Request::HASH_LENGTH
	) : Request {
		if (is_null($timeout)) {
			$timeout = new \DateTimeImmutable;
			$timeout->add(\DateInterval::createFromDateString($this->timeout));
		}
		else if ($timeout === FALSE) {
			$timeout = NULL;
		}

		return new Request($user, $type, $extraValue, $timeout, $hashLength);
	}
}
