<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Fork\ForkJob;
use SpencerMortensen\ParallelProcessor\Shell\ShellJob;
use SpencerMortensen\ParallelProcessor\Shell\ShellWorkerJob;

class SleeperJob implements ForkJob, ShellJob, ShellWorkerJob
{
	/** @var integer */
	private $input;

	/** @var string */
	private $output;

	public function __construct($input, &$output = null)
	{
		$this->input = $input;
		$this->output = &$output;
	}

	public function getCommand()
	{
		$scriptPath = dirname(__DIR__) . '/bin/sleep.php';

		return 'php' .
			' ' . escapeshellarg($scriptPath) .
			' ' . escapeshellarg($this->input);
	}

	public function run()
	{
		$sleeper = new Sleeper();
		return $sleeper->sleep($this->input);
	}

	public function stop($message)
	{
		$this->output = $message;
	}
}
