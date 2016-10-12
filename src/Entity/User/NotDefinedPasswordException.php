<?php

namespace Trejjam\Acl\Entity\User;

use Nette;
use Trejjam;

class NotDefinedPasswordException extends \LogicException implements Trejjam\Acl\Entity\User\AuthenticatorException
{

}
