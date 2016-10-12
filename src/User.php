<?php

namespace Trejjam\Acl;

use Nette;
use Trejjam;

class User extends Nette\Security\User
{
	protected $roleRepository;

	public function __construct(
		Nette\Security\IUserStorage $storage,
		Nette\Security\IAuthenticator $authenticator = NULL,
		Nette\Security\IAuthorizator $authorizator = NULL,
		Trejjam\Acl\Entity\Role\RoleRepository $roleRepository
	) {
		parent::__construct($storage, $authenticator, $authorizator);

		$this->roleRepository = $roleRepository;

		$this->guestRole = $this->roleRepository->getByName($this->guestRole, TRUE);
		$this->authenticatedRole = $this->roleRepository->getByName($this->authenticatedRole, TRUE);

		if ($this->isLoggedIn() && is_null($this->getIdentity())) {
			$storage->setAuthenticated(FALSE);
		}
	}
}
