<?php

namespace Trejjam\Acl;

use Nette;
use Doctrine;
use Trejjam;

/**
 * Class User
 *
 * @package Trejjam\Acl
 *
 * @method Doctrine\Common\Collections\Collection getRoles()
 * @method Authenticator getAuthenticator($need = TRUE)
 *
 * @method onLoggedIn(User $user)
 * @method onLoggedOut(User $user)
 */
class User extends Nette\Security\User
{
	const ROLE_GUEST         = 'guest';
	const ROLE_AUTHENTICATED = 'authenticated';

	/**
	 * @var Nette\Security\IUserStorage|UserStorage
	 */
	protected $storage;
	/**
	 * @var Entity\Role\RoleRepository
	 */
	protected $roleRepository;

	/**
	 * @var Entity\Role\Role
	 */
	public $guestRole;
	/**
	 * @var Entity\Role\Role
	 */
	public $authenticatedRole;

	public function __construct(
		Nette\Security\IUserStorage $storage,
		Nette\Security\IAuthenticator $authenticator = NULL,
		Nette\Security\IAuthorizator $authorizator = NULL,
		Trejjam\Acl\Entity\Role\RoleRepository $roleRepository
	) {
		parent::__construct($storage, $authenticator, $authorizator);

		$this->storage = $storage;
		$this->roleRepository = $roleRepository;

		try {
			try {
				$this->authenticatedRole = $this->roleRepository->getByName(self::ROLE_AUTHENTICATED, TRUE);
			}
			catch (Trejjam\Acl\Entity\Role\RoleNotFoundException $e) {
				//OR create?
				$this->authenticatedRole = new Entity\Role\Role(self::ROLE_AUTHENTICATED);
			}

			try {
				$this->guestRole = $this->roleRepository->getByName(self::ROLE_GUEST, TRUE);
			}
			catch (Trejjam\Acl\Entity\Role\RoleNotFoundException $e) {
				//OR create?
				$this->guestRole = new Entity\Role\Role(self::ROLE_GUEST, $this->authenticatedRole);
			}
		}
		catch (Doctrine\DBAL\Exception\TableNotFoundException $e) {
			$this->authenticatedRole = new Entity\Role\Role(self::ROLE_AUTHENTICATED);
			$this->guestRole = new Entity\Role\Role(self::ROLE_GUEST, $this->authenticatedRole);
		}

		if ($this->isLoggedIn() && is_null($this->getIdentity())) {
			$this->storage->setAuthenticated(FALSE);
		}
	}

	/**
	 * @param int| $username
	 * @param null $password
	 * @param bool $enableForceLogin
	 *
	 * @throws Entity\User\UserNotFoundException
	 * @throws Entity\User\NotDefinedPasswordException
	 * @throws Entity\User\InvalidCredentialsException
	 * @throws Entity\User\NotEnabledUserException
	 * @throws Entity\User\NotActivatedUserException
	 */
	public function login($username = NULL, $password = NULL, $enableForceLogin = FALSE)
	{
		$this->logout(TRUE);
		$identityHash = $this->getAuthenticator()->authenticate(func_get_args());
		$this->storage->setIdentityHash($identityHash);
		$this->storage->setAuthenticated(TRUE);
		$this->onLoggedIn($this);
	}

	public function impersonate(Trejjam\Acl\Entity\User\User $user)
	{
		$previousSessionIdentityHash = $this->storage->getSessionIdentity();
		$identityHash = $this->getAuthenticator()->authenticate([$user, NULL, TRUE]);
		$this->storage->setIdentityHash($identityHash, $previousSessionIdentityHash);
		$this->storage->setAuthenticated(TRUE);
		$this->onLoggedIn($this);
	}

	public function isImpersonated()
	{
		$identityHash = $this->storage->getSessionIdentity();
		$previousIdentityHash = $identityHash->getPreviousSessionIdentity();

		return !is_null($previousIdentityHash);
	}

	public function stopImpersonate()
	{
		$sessionIdentityHash = $this->storage->getSessionIdentity();
		$previousIdentityHash = $sessionIdentityHash->getPreviousSessionIdentity();

		$this->storage->setAuthenticated(FALSE); //destroy identityHash

		$this->storage->setSessionIdentityHash($previousIdentityHash);
		$this->storage->setAuthenticated(TRUE);
		$this->onLoggedIn($this);
	}

	/**
	 * Is a user in the specified effective role?
	 *
	 * @param string|Trejjam\Acl\Entity\Role\Role
	 *
	 * @return bool
	 */
	public function isInRole($role)
	{
		if ($role instanceof Trejjam\Acl\Entity\Role\Role) {
			$roles = $this->getRoles()->getValues();
		}
		else {
			$roles = $this->getRoles()->map(function (Trejjam\Acl\Entity\Role\Role $_role) {
				return $_role->getName();
			})->getValues();
		}

		return in_array($role, $roles, TRUE);
	}
}
