<?php

namespace Trejjam\Acl\Entity\Request;

use Trejjam\Acl\Entity;

class RequestType extends Entity\Enum
{
	const ENUM_NAME = 'userRequestType';

	const STATE_ACTIVATE      = 'activate';
	const STATE_LOST_PASSWORD = 'lostPassword';

	static public function getValues()
	{
		return [
			self::STATE_ACTIVATE,
			self::STATE_LOST_PASSWORD,
		];
	}
}
