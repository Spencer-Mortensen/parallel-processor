<?php

namespace Example;

class Sleeper
{
	public function sleep($sleepSeconds)
	{
		// define(Pi, 3.14159);
		// require 'xxx';

		sleep($sleepSeconds);

		return "Slept for {$sleepSeconds} seconds.";
	}
}
