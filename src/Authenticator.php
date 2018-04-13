<?php
declare(strict_types=1);

namespace Trejjam\Acl;

use Doctrine;
use Nette;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Trejjam;

class Authenticator implements Nette\Security\IAuthenticator
{
	/**
	 * @var Entity\User\UserRepository
	 */
	private $userRepository;
	/**
	 * @var Entity\IdentityHash\IdentityHashRepository
	 */
	private $identityHashRepository;

	/**
	 * Authenticator constructor.
	 *
	 * @param Entity\User\UserRepository                 $userRepository
	 * @param Entity\IdentityHash\IdentityHashRepository $identityHashRepository
	 */
	function __construct(
		Trejjam\Acl\Entity\User\UserRepository $userRepository,
		Trejjam\Acl\Entity\IdentityHash\IdentityHashRepository $identityHashRepository
	) {
		$this->userRepository = $userRepository;
		$this->identityHashRepository = $identityHashRepository;
	}

	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 *
	 * @param array $credentials
	 *
	 * @return Entity\IdentityHash\IdentityHash
	 * @throws Entity\User\InvalidCredentialsException
	 * @throws Entity\User\NotActivatedUserException
	 * @throws Entity\User\NotDefinedPasswordException
	 * @throws Entity\User\NotEnabledUserException
	 * @throws InvalidArgumentException
	 */
	function authenticate(array $credentials) : Trejjam\Acl\Entity\IdentityHash\IdentityHash
	{
		list($username, $password, $enableForceLogin) = $credentials + [NULL, NULL, NULL];

		if (is_null($enableForceLogin)) {
			$enableForceLogin = FALSE;
		}

		if ($username instanceof SessionUserIdentity) {
			$identityHash = $this->identityHashRepository->getByHash(
				$username->getIdentityHash(),
				Doctrine\ORM\Mapping\ClassMetadata::FETCH_EAGER
			);

			$user = $identityHash->getUser();
			$username = $user->getUsername();
		}
		else if ($username instanceof Trejjam\Acl\Entity\User\User) {
			$user = $username;
			$username = $user->getUsername();
		}
		else if (is_string($username)) {
			$user = $this->userRepository->getByUsername(
				$username,
				Doctrine\ORM\Mapping\ClassMetadata::FETCH_EAGER
			);
		}
		else {
			$className = get_class($username);
			throw new InvalidArgumentException("Unknown username type '{$className}'");
		}

		if ($enableForceLogin === FALSE) {
			if (is_null($user->getPassword())) {
				throw new Entity\User\NotDefinedPasswordException($username);
			}
			else if ( !Nette\Security\Passwords::verify($password, $user->getPassword())) {
				throw new Entity\User\InvalidCredentialsException($username);
			}
			else if ($user->getStatus() !== Entity\User\StatusType::STATE_ENABLE) {
				throw new Entity\User\NotEnabledUserException($username);
			}
			else if ( !$user->isActivated()) {
				throw new Entity\User\NotActivatedUserException($username);
			}
			else if (Nette\Security\Passwords::needsRehash($user->getPassword())) {
				$this->userRepository->changePassword($user, $password);
			}
		}

		return $this->identityHashRepository->createIdentityHash($user);
	}
}
