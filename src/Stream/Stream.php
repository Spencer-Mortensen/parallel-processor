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

namespace SpencerMortensen\ParallelProcessor\Stream;

use Exception;
use SpencerMortensen\Exceptions\Exceptions;
use SpencerMortensen\ParallelProcessor\Stream\Exceptions\CloseException;
use SpencerMortensen\ParallelProcessor\Stream\Exceptions\StreamException;
use SpencerMortensen\ParallelProcessor\Stream\Exceptions\ReadException;
use SpencerMortensen\ParallelProcessor\Stream\Exceptions\ReadIncompleteException;
use SpencerMortensen\ParallelProcessor\Stream\Exceptions\WriteException;
use SpencerMortensen\ParallelProcessor\Stream\Exceptions\WriteIncompleteException;
use Throwable;

class Stream
{
	/** @var integer */
	private static $CHUNK_SIZE = 8192;

	/** @var mixed */
	private $resource;

	public function __construct($resource)
	{
		$this->resource = $resource;
	}

	public function read()
	{
		if (!is_resource($this->resource)) {
			throw new StreamException($this->resource);
		}

		Exceptions::enable();

		try {
			$contents = self::readChunks($this->resource);
		} catch (ReadIncompleteException $exception) {
			Exceptions::disable();
			throw $exception;
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw new ReadException($throwable);
		} catch (Exception $exception) {
			Exceptions::disable();
			throw new ReadException($exception);
		}

		Exceptions::disable();
		return $contents;
	}

	private static function readChunks($resource)
	{
		for ($contents = ''; !feof($resource); $contents .= $chunk) {
			$chunk = fread($resource, self::$CHUNK_SIZE);

			if ($chunk === false) {
				$bytesRead = strlen($contents);
				throw new ReadIncompleteException($bytesRead);
			}
		}

		return $contents;
	}

	public function write($contents)
	{
		if (!is_resource($this->resource)) {
			throw new StreamException($this->resource);
		}

		Exceptions::enable();

		try {
			$bytesWritten = fwrite($this->resource, $contents);
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw new WriteException($throwable);
		} catch (Exception $exception) {
			Exceptions::disable();
			throw new WriteException($exception);
		}

		Exceptions::disable();

		$bytesTotal = strlen($contents);

		if ($bytesWritten !== $bytesTotal) {
			throw new WriteIncompleteException($bytesWritten, $bytesTotal);
		}

		return true;
	}

	public function isOpen()
	{
		return is_resource($this->resource);
	}

	public function close()
	{
		if (!is_resource($this->resource)) {
			return true;
		}

		Exceptions::enable();

		try {
			$success = fclose($this->resource);
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw new CloseException($throwable);
		} catch (Exception $exception) {
			Exceptions::disable();
			throw new CloseException($exception);
		}

		Exceptions::disable();
		return $success;
	}

	public function setBlocking()
	{
		Exceptions::enable();

		try {
			$success = stream_set_blocking($this->resource, true);
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw $throwable;
		} catch (Exception $exception) {
			Exceptions::disable();
			throw $exception;
		}

		Exceptions::disable();
		return $success;
	}

	public function setNonBlocking()
	{
		Exceptions::enable();

		try {
			$success = stream_set_blocking($this->resource, false);
		} catch (Throwable $throwable) {
			Exceptions::disable();
			throw $throwable;
		} catch (Exception $exception) {
			Exceptions::disable();
			throw $exception;
		}

		Exceptions::disable();
		return $success;
	}
}
