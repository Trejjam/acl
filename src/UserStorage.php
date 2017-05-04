<?php

namespace Trejjam\Acl;

use Doctrine;
use Nette;
use Trejjam;

class UserStorage extends Nette\Http\UserStorage
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
	 * @var Trejjam\Acl\Entity\IdentityHash\IdentityHash
	 */
	protected $identityHash;

	/**
	 * UserStorage constructor.
	 *
	 * @param Nette\Http\Session                         $sessionHandler
	 * @param Entity\User\UserRepository                 $userRepository
	 * @param Entity\IdentityHash\IdentityHashRepository $identityHashRepository
	 */
	public function __construct(
		Nette\Http\Session $sessionHandler,
		Trejjam\Acl\Entity\User\UserRepository $userRepository,
		Trejjam\Acl\Entity\IdentityHash\IdentityHashRepository $identityHashRepository
	) {
		parent::__construct($sessionHandler);
		$this->userRepository = $userRepository;
		$this->identityHashRepository = $identityHashRepository;
	}

	/**
	 * Sets the user identity.
	 *
	 * @param Nette\Security\IIdentity $identity
	 *
	 * @return static
	 * @throws LogicException
	 */
	public function setIdentity(Nette\Security\IIdentity $identity = NULL)
	{
		if ( !is_null($identity)) {
			throw new LogicException('Do not use this method, use setIdentityHash instead');
		}

		$this->identityHash = NULL;
		$this->getSessionSection(TRUE)->identity = NULL;

		return $this;
	}

	/**
	 * Sets the user identity hash.
	 *
	 * @param Entity\IdentityHash\IdentityHash|null $identityHash
	 * @param SessionUserIdentity|null              $previousSessionUserIdentity
	 *
	 * @return static
	 */
	public function setIdentityHash(
		Trejjam\Acl\Entity\IdentityHash\IdentityHash $identityHash = NULL,
		SessionUserIdentity $previousSessionUserIdentity = NULL
	) {
		$identity = NULL;


		if ( !is_null($identityHash)) {
			$identity = new SessionUserIdentity($identityHash->getHash(), $identityHash->getUser()->getId());

			if ( !is_null($previousSessionUserIdentity)) {
				$identity->setPreviousSessionIdentity($previousSessionUserIdentity);
			}
		}

		$this->setSessionIdentityHash($identity);
		$this->identityHash = $identityHash;

		return $this;
	}

	public function setSessionIdentityHash(SessionUserIdentity $sessionUserIdentity)
	{
		$session = $this->getSessionSection(TRUE);
		$session->identity = $sessionUserIdentity;

		$this->identityHash = NULL;

		return $this;
	}

	public function setAuthenticated($state)
	{
		if ($state === FALSE) {
			$session = $this->getSessionSection(TRUE);

			$identityHash = $this->getIdentityHash($session, FALSE);

			if ( !is_null($identityHash)) {
				$this->identityHashRepository->destroyIdentityHash($identityHash);
			}
		}

		return parent::setAuthenticated($state);
	}

	/**
	 * @return Nette\Security\IIdentity|Entity\User\User|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getIdentity()
	{
		$session = $this->getSessionSection(FALSE);

		$identityHash = $this->getIdentityHash($session);

		$identity = NULL;
		if ( !is_null($identityHash)) {
			$identity = $identityHash->getUser();
		}

		return $identity;
	}

	/**
	 * @param Nette\Http\SessionSection|NULL $session
	 * @param bool                           $validateHash
	 *
	 * @return null|Entity\IdentityHash\IdentityHash
	 */
	public function getIdentityHash(Nette\Http\SessionSection $session = NULL, $validateHash = TRUE)
	{
		if (is_null($this->identityHash)) {
			$sessionIdentity = $this->getSessionIdentity($session);

			if ( !is_null($sessionIdentity)) {
				try {
					$identityHash = $this->identityHashRepository->getByHash($sessionIdentity->getIdentityHash(), Doctrine\ORM\Mapping\ClassMetadata::FETCH_EAGER);
				}
				catch (Trejjam\Acl\Entity\IdentityHash\IdentityHashNotFoundException $e) {
					return NULL;
				}

				if ($validateHash === TRUE) {
					$this->identityHash = $this->identityHashValidate($sessionIdentity, $identityHash);
				}
			}
		}

		return $this->identityHash;
	}

	/**
	 * @param Nette\Http\SessionSection|null $session
	 *
	 * @return SessionUserIdentity
	 */
	public function getSessionIdentity(Nette\Http\SessionSection $session = NULL)
	{
		$session = $session ?: $this->getSessionSection(FALSE);

		if (
			!is_null($session)
			&& !is_null($sessionIdentity = $session->identity)
		) {
			return $sessionIdentity;
		}

		return NULL;
	}

	/**
	 * @param SessionUserIdentity              $sessionIdentity
	 * @param Entity\IdentityHash\IdentityHash $identityHash
	 *
	 * @return null|Entity\IdentityHash\IdentityHash
	 */
	protected function identityHashValidate(SessionUserIdentity $sessionIdentity, Trejjam\Acl\Entity\IdentityHash\IdentityHash $identityHash)
	{
		if ($sessionIdentity->getUserId() !== $identityHash->getUser()->getId()) {
			return NULL;
		}

		switch ($identityHash->getAction()) {
			case Trejjam\Acl\Entity\IdentityHash\IdentityHashStatus::STATE_NONE:
			case Trejjam\Acl\Entity\IdentityHash\IdentityHashStatus::STATE_RELOAD:
				return $identityHash;

			default:
				return NULL;
		}
	}
}
