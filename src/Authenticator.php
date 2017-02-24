<?php

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
	 * Authenticator constructor.
	 *
	 * @param Entity\User\UserRepository $userRepository
	 */
	function __construct(
		Trejjam\Acl\Entity\User\UserRepository $userRepository
	) {
		$this->userRepository = $userRepository;
	}

	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 *
	 * @param array $credentials
	 *
	 * @return IIdentity|Entity\User\User
	 * @throws Entity\User\UserNotFoundException
	 * @throws Entity\User\NotDefinedPasswordException
	 * @throws Entity\User\InvalidCredentialsException
	 * @throws Entity\User\NotEnabledUserException
	 * @throws Entity\User\NotActivatedUserException
	 */
	function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$user = $this->userRepository->getByUsername($username, Doctrine\ORM\Mapping\ClassMetadata::FETCH_EAGER);

		if (is_null($user->getPassword())) {
			throw new Entity\User\NotDefinedPasswordException($username);
		}
		else if ( !Nette\Security\Passwords::verify($password, $user->getPassword())) {
			throw new Entity\User\InvalidCredentialsException($username);
		}
		else if ($user->getStatus() !== Entity\User\StatusType::STATE_ENABLE) {
			throw new Entity\User\NotEnabledUserException($username);
		}
		else if ($user->getActivated() !== Entity\User\StatusActivated::STATE_ACTIVATED) {
			throw new Entity\User\NotActivatedUserException($username);
		}
		else if (Nette\Security\Passwords::needsRehash($user->getPassword())) {
			$this->userRepository->changePassword($user, $password);
		}

		return $user;
	}
}
