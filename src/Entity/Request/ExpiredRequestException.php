<?php
declare(strict_types=1);

namespace Trejjam\Acl\Entity\Request;

use Trejjam\Acl;

class ExpiredRequestException extends Acl\LogicException implements InvalidRequestException
{

}
