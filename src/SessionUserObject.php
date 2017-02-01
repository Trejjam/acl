<?php
/**
 * Created by PhpStorm.
 * User: trejbal
 * Date: 01.02.2017
 * Time: 19:28
 */

namespace Trejjam\Acl;

use Nette;
use App;
use Trejjam;

class SessionUserObject implements Nette\Security\IIdentity
{
	/**
	 * @var int
	 */
	private $id;

	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * Returns the ID of user.
	 *
	 * @return mixed
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 *
	 * @return array
	 */
	function getRoles()
	{
		throw new Trejjam\Utils\RuntimeException();
	}
}
