<?php

namespace Trejjam\Acl;

use Nette;
use Doctrine;
use Trejjam;

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
				$this->guestRole = $this->roleRepository->getByName(self::ROLE_GUEST, TRUE);
			}
			catch (Trejjam\Acl\Entity\Role\RoleNotFoundException $e) {
				//OR create?
				$this->guestRole = new Entity\Role\Role(self::ROLE_GUEST);
			}

			try {
				$this->authenticatedRole = $this->roleRepository->getByName(self::ROLE_AUTHENTICATED, TRUE);
			}
			catch (Trejjam\Acl\Entity\Role\RoleNotFoundException $e) {
				//OR create?
				$this->authenticatedRole = new Entity\Role\Role(self::ROLE_AUTHENTICATED);
			}
		}
		catch (Doctrine\DBAL\Exception\TableNotFoundException $e) {
			$this->guestRole = new Entity\Role\Role(self::ROLE_GUEST);
			$this->authenticatedRole = new Entity\Role\Role(self::ROLE_AUTHENTICATED);
		}

		if ($this->isLoggedIn() && is_null($this->getIdentity())) {
			$storage->setAuthenticated(FALSE);
		}
	}
}
