<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Processor;
use SpencerMortensen\ParallelProcessor\Shell\Shell;
use Throwable;

require 'bootstrap.php';

$results = array();

$processor = new Processor();

try {
	$t0 = microtime(true);

	$processor->start(new Shell(new SleeperJob(2, $results[])));
	$processor->start(new Shell(new SleeperJob(3, $results[])));
	$processor->start(new Shell(new SleeperJob(1, $results[])));

	$processor->finish();

	$t1 = microtime(true);

	echo "results: ", json_encode($results), "\n";
	echo "\nTotal time: ", $t1 - $t0, " seconds\n";

} catch (Throwable $throwable) {
	$message = $throwable->getMessage();

	echo "exception:\n", var_export($throwable), "\n\n";
	echo "message: {$message}\n";
}
