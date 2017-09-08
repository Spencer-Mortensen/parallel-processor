<?php

namespace Example;

class Sleeper
{
	public function sleep($sleepSeconds)
	{
		sleep($sleepSeconds);

		return "Slept for {$sleepSeconds} seconds.";
	}
}
