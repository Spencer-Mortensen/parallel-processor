<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Processor;
use SpencerMortensen\ParallelProcessor\Fork\Fork;
use Throwable;

require 'bootstrap.php';

$results = array();

$processor = new Processor();

try {
	$t0 = microtime(true);

	$processor->start(new Fork(new SleeperJob(2, $results[])));
	$processor->start(new Fork(new SleeperJob(3, $results[])));
	$processor->start(new Fork(new SleeperJob(1, $results[])));

	$processor->finish();

	$t1 = microtime(true);

	echo "results: ", json_encode($results), "\n";
	echo "\nTotal time: ", $t1 - $t0, " seconds\n";

} catch (Throwable $throwable) {
	//	echo "error: ", var_export($throwable, true), "\n";
	$message = $throwable->getMessage();
	echo "error: {$message}\n";
}
