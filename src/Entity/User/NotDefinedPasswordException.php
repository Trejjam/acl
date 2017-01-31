<?php

namespace Trejjam\Acl\Entity\User;

use Trejjam\Acl;

class NotDefinedPasswordException extends Acl\LogicException implements Acl\Entity\User\AuthenticatorException
{

}
