<?php

namespace Trejjam\Acl\Entity\IdentityHash;

use Nette;
use Trejjam;

class IdentityHashService
{
	/**
	 * @var Nette\Http\Request
	 */
	private $request;

	public function __construct(Nette\Http\Request $request)
	{
		$this->request = $request;
	}

	/**
	 * @param Trejjam\Acl\Entity\User\User $user
	 * @param string                       $ip
	 * @param int                          $hashLength
	 *
	 * @return IdentityHash
	 */
	public function createIdentityHash(Trejjam\Acl\Entity\User\User $user, $ip = NULL, $hashLength = IdentityHash::HASH_LENGTH)
	{
		if (is_null($ip)) {
			$ip = $this->request->getRemoteAddress();
		}

		return new IdentityHash($user, $ip, $hashLength);
	}
}
