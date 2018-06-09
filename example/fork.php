<?php

namespace Example;

use Exception;
use SpencerMortensen\ParallelProcessor\Processor;
use SpencerMortensen\ParallelProcessor\Fork\ForkProcess;
use Throwable;

require 'autoload.php';

$results = [];

$processor = new Processor();

try {
	$t0 = microtime(true);

	$processor->start(new ForkProcess(new SleeperJob(2, $results[])));
	$processor->start(new ForkProcess(new SleeperJob(3, $results[])));
	$processor->start(new ForkProcess(new SleeperJob(1, $results[])));

	$processor->finish();

	$t1 = microtime(true);

	echo "results: ", json_encode($results), "\n";
	echo "\nTotal time: ", $t1 - $t0, " seconds\n";

} catch (Throwable $throwable) {
	$message = $throwable->getMessage();

	echo "exception:\n", var_export($throwable), "\n\n";
	echo "message: {$message}\n";
} catch (Exception $exception) {
	$message = $exception->getMessage();

	echo "exception:\n", var_export($exception), "\n\n";
	echo "message: {$message}\n";
}

