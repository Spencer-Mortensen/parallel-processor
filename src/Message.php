<?php

namespace SpencerMortensen\ParallelProcessor;

use Exception;
use SpencerMortensen\Exceptions\Exceptions;
use Throwable;

class Message
{
	const TYPE_ERROR = 0;
	const TYPE_RESULT = 1;

	public static function serialize($type, $value)
	{
		$data = array(
			$type => $value
		);

		return serialize($data);
	}

	public static function deserialize($serialized)
	{
		Exceptions::enable();

		try {
			$data = unserialize($serialized);
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw ProcessorException::invalidMessage($serialized);
		} catch (Exception $exception) {
			Exceptions::disable();
			throw ProcessorException::invalidMessage($serialized);
		}

		Exceptions::disable();

		if (!is_array($data) || (count($data) === 0)) {
			throw ProcessorException::invalidMessage($serialized);
		}

		list($type, $value) = each($data);

		if ($type === self::TYPE_RESULT) {
			return $value;
		}

		if (($value instanceof Throwable) || ($value instanceof Exception)) {
			throw $value;
		}

		throw ProcessorException::invalidMessage($serialized);
	}
}
