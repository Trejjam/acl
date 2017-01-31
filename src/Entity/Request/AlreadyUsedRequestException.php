<?php

namespace Trejjam\Acl\Entity\Request;

use Trejjam\Acl;

class AlreadyUsedRequestException extends Acl\LogicException implements InvalidRequestException
{

}
