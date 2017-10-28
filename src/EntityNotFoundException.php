<?php
declare(strict_types=1);

namespace Trejjam\Acl;

class EntityNotFoundException extends InvalidArgumentException
{
	/**
	 * @var string
	 */
	protected $exceptionType;

	public function __construct($message = "", $code = 0, \Exception $previous = NULL)
	{
		if ($code instanceof \Exception && is_null($previous)) {
			$previous = $code;
			$code = 0;
		}
		parent::__construct($message, $code, $previous);

		$this->exceptionType = static::class;
	}

	public function getExceptionType(bool $withNamespace = TRUE)
	{
		if ($withNamespace) {
			return $this->exceptionType;
		}
		else {
			$namespaceArray = explode('\\', $this->exceptionType);

			return $namespaceArray[count($namespaceArray) - 1];
		}
	}
}
