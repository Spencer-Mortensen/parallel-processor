<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Job;

class SleepJob implements Job
{
	private $sleepSeconds;

	public function __construct($sleepSeconds)
	{
		$this->sleepSeconds = $sleepSeconds;
	}

	public function run()
	{
		sleep($this->sleepSeconds);

		return $this->sleepSeconds;
	}
}
