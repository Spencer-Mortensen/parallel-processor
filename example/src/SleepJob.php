<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Shell\ShellJob;

class SleepJob implements ShellJob
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

	public function run($send)
	{
		$sleeper = new Sleeper();
		$message = $sleeper->sleep($this->input);

		call_user_func($send, $message);
	}

	public function receive($message)
	{
		$this->output = $message;
	}

	public function getCommand()
	{
		$scriptPath = dirname(__DIR__) . '/bin/sleep.php';

		return 'php' .
			' ' . escapeshellarg($scriptPath) .
			' ' . escapeshellarg($this->input);
	}
}
