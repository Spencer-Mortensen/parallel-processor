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

namespace SpencerMortensen\ParallelProcessor\Fork;

use Exception;
use SpencerMortensen\Exceptions\Exceptions;
use SpencerMortensen\ParallelProcessor\Message;
use SpencerMortensen\ParallelProcessor\ProcessorException;
use SpencerMortensen\ParallelProcessor\Process;
use SpencerMortensen\ParallelProcessor\Stream\Stream;
use Throwable;

class Fork implements Process
{
	/** @var ForkJob */
	private $job;

	public function __construct(ForkJob $job)
	{
		$this->job = $job;
	}

	public function start()
	{
		list($resourceA, $resourceB) = $this->getStreamPair();

		$a = new Stream($resourceA);
		$b = new Stream($resourceB);

		$pid = $this->fork();

		// Worker process
		if ($pid === 0) {
			$message = $this->runJob();

			try {
				$a->close();
				$b->write($message);
				$b->close();
			} catch (Exception $exception) {
				exit(1);
			}

			exit(0);
		}

		// Master process
		if (0 < $pid) {
			$b->close();
			$a->setNonBlocking();

			return $resourceA;
		}

		throw ProcessorException::forkError();
	}

	private function getStreamPair()
	{
		Exceptions::enable();

		try {
			$pair = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw $throwable;
		} catch (Exception $exception) {
			Exceptions::disable();
			throw $exception;
		}

		Exceptions::disable();

		return $pair;
	}

	private function fork()
	{
		Exceptions::enable();

		try {
			$pid = pcntl_fork();
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw $throwable;
		} catch (Exception $exception) {
			Exceptions::disable();
			throw $exception;
		}

		Exceptions::disable();
		return $pid;
	}

	private function runJob()
	{
		Exceptions::enable();

		try {
			$result = $this->job->run();
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

	public function stop($result)
	{
		$this->job->stop($result);
	}
}
