<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\User;

use Trejjam\Acl;

class InvalidCredentialsException extends Acl\LogicException implements Acl\Entity\User\AuthenticatorException
{

}
