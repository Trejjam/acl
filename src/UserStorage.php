<?php
declare(strict_types=1);

namespace Trejjam\Acl;

use Doctrine;
use Mangoweb\Clock\Clock;
use Nette;
use Trejjam;

class UserStorage extends Nette\Http\UserStorage implements Nette\Security\IUserStorage
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
	 * @var Entity\IdentityHash\IdentityHash|null
	 */
	protected $identityHash;

	public function __construct(
		Nette\Http\Session $sessionHandler,
		Trejjam\Acl\Entity\User\UserRepository $userRepository,
		Trejjam\Acl\Entity\IdentityHash\IdentityHashRepository $identityHashRepository
	) {
		parent::__construct($sessionHandler);

		$this->userRepository = $userRepository;
		$this->identityHashRepository = $identityHashRepository;
	}

	public function setIdentity(Nette\Security\IIdentity $identity = NULL) : self
	{
		if ( !is_null($identity)) {
			throw new LogicException('Do not use this method, use setIdentityHash instead');
		}

		$this->identityHash = NULL;
		$session = $this->getSessionSection(TRUE);
		assert(!is_null($session));
		$session->__set('identity', NULL);

		return $this;
	}

	public function setIdentityHash(
		Entity\IdentityHash\IdentityHash $identityHash = NULL,
		SessionUserIdentity $previousSessionUserIdentity = NULL
	) : self {
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

	public function setSessionIdentityHash(?SessionUserIdentity $sessionUserIdentity) : self
	{
		$session = $this->getSessionSection(TRUE);
		assert(!is_null($session));
		$session->__set('identity', $sessionUserIdentity);

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

		parent::setAuthenticated($state);

		$section = $this->getSessionSection(TRUE);
		$section->__set('authTime', Clock::now()->getTimestamp());

		return $this;
	}

	public function getAuthTime() : ?\DateTimeImmutable
	{
		if ( !$this->isAuthenticated()) {
			return NULL;
		}

		$section = $this->getSessionSection(TRUE);

		return new \DateTimeImmutable('@' . $section->__get('authTime'));
	}

	/**
	 * @return Nette\Security\IIdentity|Entity\User\User|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getIdentity() : ?Nette\Security\IIdentity
	{
		$session = $this->getSessionSection(FALSE);

		$identityHash = $this->getIdentityHash($session);

		$identity = NULL;
		if ( !is_null($identityHash)) {
			$identity = $identityHash->getUser();
		}

		return $identity;
	}

	public function getIdentityHash(
		Nette\Http\SessionSection $session = NULL,
		bool $validateHash = TRUE
	) : ?Entity\IdentityHash\IdentityHash {
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

	public function getSessionIdentity(Nette\Http\SessionSection $session = NULL) : ?SessionUserIdentity
	{
		$session = $session ?? $this->getSessionSection(FALSE);

		if (
			!is_null($session)
			&& !is_null($sessionIdentity = $session->__get('identity'))
		) {
			return $sessionIdentity;
		}

		return NULL;
	}

	protected function identityHashValidate(
		SessionUserIdentity $sessionIdentity,
		Entity\IdentityHash\IdentityHash $identityHash
	) : ?Entity\IdentityHash\IdentityHash {
		if ($sessionIdentity->getUserId() !== $identityHash->getUser()->getId()) {
			return NULL;
		}

		switch ($identityHash->getAction()) {
			case Trejjam\Acl\Entity\IdentityHash\IdentityHashStatus::STATE_NONE:
			case Trejjam\Acl\Entity\IdentityHash\IdentityHashStatus::STATE_RELOAD:
			case Trejjam\Acl\Entity\IdentityHash\IdentityHashStatus::STATE_REQUIRE_SECOND_FACTOR:
				return $identityHash;

			default:
				return NULL;
		}
	}
}
