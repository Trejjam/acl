<?php
declare(strict_types=1);

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

	public function createIdentityHash(
		Trejjam\Acl\Entity\User\User $user,
		string $ip = NULL,
		int $hashLength = IdentityHash::HASH_LENGTH
	) : IdentityHash {
		if (is_null($ip)) {
			$ip = $this->request->getRemoteAddress();

		}
		if (is_null($ip)) {
			throw new Trejjam\Acl\InvalidArgumentException;
		}

		return new IdentityHash($user, $ip, $hashLength);
	}
}
