<?php

namespace Trejjam\Acl\Entity\IdentityHash;

use Trejjam;

class IdentityHashService
{
	/**
	 * @param Trejjam\Acl\Entity\User\User $user
	 * @param string                       $ip
	 * @param int                          $hashLength
	 *
	 * @return IdentityHash
	 */
	public function createIdentityHash(Trejjam\Acl\Entity\User\User $user, $ip, $hashLength = IdentityHash::HASH_LENGTH)
	{
		return new IdentityHash($user, $ip, $hashLength);
	}
}
