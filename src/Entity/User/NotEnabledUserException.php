<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\User;

use Trejjam\Acl;

class NotEnabledUserException extends Acl\LogicException implements Acl\Entity\User\AuthenticatorException
{

}
