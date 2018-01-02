<?php

/**
 * Copyright (C) 2017 Spencer Mortensen
 *
 * This file is part of parallel-processor.
 *
 * Parallel-processor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Parallel-processor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with parallel-processor. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Spencer Mortensen <spencer@lens.guide>
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0
 * @copyright 2017 Spencer Mortensen
 */

namespace SpencerMortensen\ParallelProcessor\Shell;

use ErrorException;
use Exception;
use SpencerMortensen\Exceptions\Exceptions;
use SpencerMortensen\ParallelProcessor\Message;
use Throwable;

class ShellWorker
{
	const CODE_SUCCESS = 0;
	const CODE_FAILURE = 1;

	public function run(ShellWorkerJob $job)
	{
		ini_set('display_errors', 'Off');

		register_shutdown_function(array($this, 'shutdownFunction'));

		$message = $this->getMessage($job);

		$this->send($message);
	}

	public function reply($result)
	{
		$message = Message::serialize(Message::TYPE_RESULT, $result);

		$this->send($message);
	}

	private function getMessage(ShellWorkerJob $job)
	{
		Exceptions::enable();

		try {
			$result = $job->run();
		} catch (Throwable $throwable) {
			Exceptions::disable();
			return Message::serialize(Message::TYPE_ERROR, $throwable);
		} catch (Exception $exception) {
			Exceptions::disable();
			return Message::serialize(Message::TYPE_ERROR, $exception);
		}

		Exceptions::disable();
		return Message::serialize(Message::TYPE_RESULT, $result);
	}

	private function send($message)
	{
		$path = 'php://fd/' . Shell::STDOUT;

		file_put_contents($path, $message . "\n");

		exit;
	}

	public function shutdownFunction()
	{
		$error = error_get_last();
		error_clear_last();

		if (!is_array($error)) {
			return;
		}

		$level = $error['type'];
		$message = trim($error['message']);
		$file = $error['file'];
		$line = $error['line'];
		$code = null;

		$exception = new ErrorException($message, $code, $level, $file, $line);
		$message = Message::serialize(Message::TYPE_ERROR, $exception);

		$this->send($message);
	}
}
