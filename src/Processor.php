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
 * @author Spencer Mortensen <spencer@testphp.org>
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0
 * @copyright 2017 Spencer Mortensen
 */

namespace SpencerMortensen\ParallelProcessor;

use Exception;

class Processor
{
	private static $CHUNK_SIZE = 8192;

	/** @var array */
	private $jobs;

	/** @var array */
	private $results;

	/** @var integer */
	private $timeoutSeconds;

	/** @var integer */
	private $timeoutMicroseconds;

	public function __construct($maximumBlockingSeconds = null)
	{
		$this->setTimeouts($maximumBlockingSeconds);
		$this->jobs = array();
		$this->results = array();
	}

	private function setTimeouts($seconds)
	{
		if ($seconds === null) {
			$this->timeoutSeconds = null;
			$this->timeoutMicroseconds = null;
		} else {
			$this->timeoutSeconds = (integer)$seconds;
			$this->timeoutMicroseconds = self::fromSecondsToMicroseconds($seconds - $this->timeoutSeconds);
		}
	}

	private static function fromSecondsToMicroseconds($seconds)
	{
		return (integer)round($seconds * 1000000);
	}

	public function startJob($id, Job $job)
	{
		$this->jobs[$id] = $this->getJobStream($job);
	}

	private function getJobStream(Job $job)
	{
		list($a, $b) = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

		if ($a === null) {
			throw new Exception('Unable to create a stream socket pair');
		}

		$pid = pcntl_fork();

		if ($pid === 0) {
			fclose($b);
			self::write($a, $job->run());
			fclose($a);

			exit(0);
		}

		if (0 < $pid) {
			fclose($a);

			stream_set_blocking($b, false);
			stream_set_chunk_size($b, self::$CHUNK_SIZE);

			return $b;
		}

		throw new Exception('Unable to fork the current process');
	}

	public function getResult(&$id, &$result)
	{
		return $this->getReadyResult($id, $result) || (
			$this->waitForResult() &&
			$this->getReadyResult($id, $result)
		);
	}

	private function getReadyResult(&$id, &$result)
	{
		if (count($this->results) === 0) {
			return false;
		}

		$id = key($this->results);
		$result = $this->results[$id];

		unset($this->results[$id]);
		return true;
	}

	private function waitForResult()
	{
		if (count($this->jobs) === 0) {
			return false;
		}

		$ready = $this->jobs;
		$x = null;

		if (stream_select($ready, $x, $x, $this->timeoutSeconds, $this->timeoutMicroseconds) === 0) {
			throw new Exception('No jobs completed within the timeout period');
		}

		foreach ($ready as $id => $stream) {
			unset($this->jobs[$id]);
			$this->results[$id] = self::read($stream);
			fclose($stream);
		}

		return true;
	}

	private static function read($stream)
	{
		for ($output = ''; !feof($stream); $output .= $data) {
			$data = fread($stream, self::$CHUNK_SIZE);

			if ($data === false) {
				throw new Exception('Unable to read from the socket stream');
			}
		}

		return $output;
	}

	private static function write($stream, $data)
	{
		if (fwrite($stream, $data) !== strlen($data)) {
			throw new Exception('Unable to write to the socket stream');
		}
	}
}
