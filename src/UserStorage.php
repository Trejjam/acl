<?php

namespace Trejjam\Acl;

use Nette;
use Trejjam;

class UserStorage extends Nette\Http\UserStorage
{
	/**
	 * @var bool
	 */
	private $autoFetchUser;
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

	/**
	 * Sets the user identity.
	 *
	 * @param Nette\Security\IIdentity $identity
	 *
	 * @return static
	 */
	public function setIdentity(Nette\Security\IIdentity $identity = NULL)
	{
		if ( !is_null($identity)) {
			$this->identity = $identity;
			$identity = new SessionUserObject($identity->getId());
		}

		$this->getSessionSection(TRUE)->identity = $identity;

		return $this;
	}

	/**
	 * @return Nette\Security\IIdentity|Entity\User\User|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getIdentity()
	{
		$session = $this->getSessionSection(FALSE);

		$identity = NULL;

		if ($this->identity) {
			$identity = $this->identity;
		}
		else if ($session && !is_null($session->identity)) {
			$identity = $this->identity = $session->identity;
		}

		if (
			!is_null($identity)
			&& !($identity instanceof Trejjam\Acl\Entity\User\User)
		) {
			$identity = $this->identity = $this->userRepository->getById($identity->getId());
		}

		return $identity;
	}
}
