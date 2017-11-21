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

namespace SpencerMortensen\ParallelProcessor;

class Stream
{
	/** @var integer */
	private static $CHUNK_SIZE = 8192;

	/** @var resource */
	private $resource;

	public function __construct($resource)
	{
		$this->resource = $resource;
	}

	public function isOpen()
	{
		return is_resource($this->resource);
	}

	public function read()
	{
		if (!is_resource($this->resource)) {
			throw ParallelProcessorException::readError($this->resource, null);
		}

		for ($contents = ''; !feof($this->resource); $contents .= $chunk) {
			$chunk = fread($this->resource, self::$CHUNK_SIZE);

			if ($chunk === false) {
				throw ParallelProcessorException::readError($this->resource, $contents);
			}
		}

		return $contents;
	}

	public function write($contents)
	{
		if (!is_resource($this->resource)) {
			throw ParallelProcessorException::writeError($this->resource, $contents);
		}

		if (fwrite($this->resource, $contents) !== strlen($contents)) {
			throw ParallelProcessorException::writeError($this->resource, $contents);
		}
	}

	public function close()
	{
		return fclose($this->resource);
	}
}
