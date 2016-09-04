<?php

namespace Trejjam\Acl;

use Nette;
use Trejjam;

class UserStorage extends Nette\Http\UserStorage
{
	/**
	 * @var Entity\User\UserRepository
	 */
	private $userRepository;
	/**
	 * @var Trejjam\Acl\Entity\User\User|Nette\Security\IIdentity
	 */
	protected $identity;

	/**
	 * UserStorage constructor.
	 *
	 * @param Nette\Http\Session         $sessionHandler
	 * @param Entity\User\UserRepository $userRepository
	 */
	public function __construct(
		Nette\Http\Session $sessionHandler,
		Trejjam\Acl\Entity\User\UserRepository $userRepository
	) {
		parent::__construct($sessionHandler);
		$this->userRepository = $userRepository;
	}

	public function getIdentity()
	{
		$session = $this->getSessionSection(FALSE);

		$identity = NULL;

		if ($this->identity) {
			$identity = $this->identity;
		}
		else if ($session) {
			$identity = $this->identity = $this->userRepository->getById($session->identity->getId());
		}

		return $identity;
	}
}
