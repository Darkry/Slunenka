<?php

use Nette\Security as NS;


/**
 * Users authenticator.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class Authenticator extends Nette\Object implements NS\IAuthenticator
{
	/** @var Doctrine\ORM\EntityRepository */
	private $repository;



	public function __construct(Doctrine\ORM\EntityRepository $repository)
	{
		$this->repository = $repository;
	}



	/**
	 * Performs an authentication
	 * @param  array
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$entity = $this->repository->findOneBy(array("userName" => $username));

		if (!$entity) {
			throw new NS\AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
		}

		if ($entity->getPassword() !== $this->calculateHash($password)) {
			throw new NS\AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
		}

		return new NS\Identity($entity->getId(), $entity->getRole()->getName(), array("userName" => $username, "realName" => $entity->getRealName(), "email" => $entity->getEmail()));
	}

        public function canBeAuthenticated(array $credentials) {
            	list($username, $password) = $credentials;
		$entity = $this->repository->findOneBy(array("userName" => $username));

		if (!$entity) {
			return false;
		}

		if ($entity->getPassword() !== $this->calculateHash($password)) {
			return false;
		}

		return true;
        }



	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public function calculateHash($password)
	{
		return sha1(md5($password . str_repeat('jop,jop,su65=456řžčper!', 10)));
	}

}
