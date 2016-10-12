<?php

namespace Trejjam\Acl\Entity\User;

use Trejjam;

class InvalidCredentialsException extends \LogicException implements Trejjam\Acl\Entity\User\AuthenticatorException
{

}