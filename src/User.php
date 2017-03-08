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
 */
class User extends Nette\Security\User
{
	const ROLE_GUEST         = 'guest';
	const ROLE_AUTHENTICATED = 'authenticated';

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
			$storage->setAuthenticated(FALSE);
		}
	}

	/**
	 * Is a user in the specified effective role?
	 *
	 * @param  string
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
