<?php
declare(strict_types=1);

namespace Trejjam\Acl;

use Nette;
use Doctrine;
use Trejjam;

/**
 * @method Doctrine\Common\Collections\Collection|Entity\Role\Role[] getRoles()
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
	 * @var UserStorage
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
		UserStorage $storage,
		Nette\Security\IAuthenticator $authenticator,
		Nette\Security\IAuthorizator $authorizator,

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

		if (
			$this->storage->isAuthenticated()
			&& is_null($this->getIdentity())
		) {
			$this->storage->setAuthenticated(FALSE);
		}
	}

	/**
	 * @param Entity\User\User|string|null $username
	 * @param string|null                  $password
	 * @param bool                         $enableForceLogin
	 *
	 * @throws Entity\User\UserNotFoundException
	 * @throws Entity\User\NotDefinedPasswordException
	 * @throws Entity\User\InvalidCredentialsException
	 * @throws Entity\User\NotEnabledUserException
	 * @throws Entity\User\NotActivatedUserException
	 */
	public function login(
		$username = NULL,
		$password = NULL,
		bool $enableForceLogin = FALSE
	) : void {
		$this->logout(TRUE);

		$identityHash = $this->getAuthenticator()->authenticate(func_get_args());

		$this->storage->setIdentityHash($identityHash);
		$this->storage->setAuthenticated(TRUE);

		$this->onLoggedIn($this);
	}

	public function isLoggedIn() : bool
	{
		if ( !$this->storage->isAuthenticated()) {
			return FALSE;
		}

		return !in_array($this->storage->getIdentityHash()->getAction(), [
			Entity\IdentityHash\IdentityHashStatus::STATE_REQUIRE_SECOND_FACTOR,
			Entity\IdentityHash\IdentityHashStatus::STATE_DESTROYED,
		], TRUE);
	}

	public function impersonate(Trejjam\Acl\Entity\User\User $user) : void
	{
		$previousSessionIdentityHash = $this->storage->getSessionIdentity();
		$identityHash = $this->getAuthenticator()->authenticate([$user, NULL, TRUE]);
		$this->storage->setIdentityHash($identityHash, $previousSessionIdentityHash);
		$this->storage->setAuthenticated(TRUE);
		$this->onLoggedIn($this);
	}

	public function isImpersonated() : bool
	{
		$identityHash = $this->storage->getSessionIdentity();
		if (is_null($identityHash)) {
			return FALSE;
		}

		$previousIdentityHash = $identityHash->getPreviousSessionIdentity();

		return !is_null($previousIdentityHash);
	}

	public function stopImpersonate() : void
	{
		$sessionIdentityHash = $this->storage->getSessionIdentity();
		if (is_null($sessionIdentityHash)) {
			$this->logout();
		}
		else {
			$previousIdentityHash = $sessionIdentityHash->getPreviousSessionIdentity();

			$this->storage->setAuthenticated(FALSE); //destroy identityHash

			$this->storage->setSessionIdentityHash($previousIdentityHash);
			$this->storage->setAuthenticated(TRUE);
			$this->onLoggedIn($this);
		}
	}

	/**
	 * Is a user in the specified effective role?
	 *
	 * @param string|Trejjam\Acl\Entity\Role\Role $role
	 *
	 * @return bool
	 */
	public function isInRole($role) : bool
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
