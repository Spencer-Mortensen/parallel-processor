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

use SpencerMortensen\ParallelProcessor\ProcessorException;
use SpencerMortensen\ParallelProcessor\Process;

class Shell implements Process
{
	/** @var integer */
	const STDOUT = 1;

	/** @var integer */
	const STDERR = 2;

	/** @var ShellJob */
	private $job;

	/** @var null|resource */
	private $process;

	public function __construct(ShellJob $job)
	{
		$this->job = $job;
	}

	public function start()
	{
		$descriptor = array(
			self::STDOUT => array('pipe', 'w'),
			self::STDERR => array('pipe', 'w')
		);

		$command = $this->job->getCommand();
		$process = proc_open($command, $descriptor, $pipes);

		if (!is_resource($process)) {
			throw ProcessorException::openProcessError();
		}

		fclose($pipes[self::STDERR]);

		$this->process = $process;
		return $pipes[self::STDOUT];
	}

	public function stop($result)
	{
		if (is_resource($this->process)) {
			proc_close($this->process);
		}

		$this->job->stop($result);
	}
}
