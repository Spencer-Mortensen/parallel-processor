<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Shell\ShellProcessor;

require 'bootstrap.php';

$results = array();

$processor = new ShellProcessor();

$t0 = microtime(true);

$processor->start(new SleepJob(2, $results[]));
$processor->start(new SleepJob(3, $results[]));
$processor->start(new SleepJob(1, $results[]));

$processor->finish();

$t1 = microtime(true);

echo implode("\n", $results), "\n";
echo "\nTotal time: ", $t1 - $t0, " seconds\n";
