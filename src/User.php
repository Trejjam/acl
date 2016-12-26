<?php

namespace Trejjam\Acl;

use Nette;
use Doctrine;
use Trejjam;

class User extends Nette\Security\User
{
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
			$this->guestRole = $this->roleRepository->getByName($this->guestRole, TRUE);
			$this->authenticatedRole = $this->roleRepository->getByName($this->authenticatedRole, TRUE);
		}
		catch (Doctrine\DBAL\Exception\TableNotFoundException $e) {
			$this->guestRole = new Entity\Role\Role($this->guestRole);
			$this->authenticatedRole = new Entity\Role\Role($this->authenticatedRole);
		}

		if ($this->isLoggedIn() && is_null($this->getIdentity())) {
			$storage->setAuthenticated(FALSE);
		}
	}
}
