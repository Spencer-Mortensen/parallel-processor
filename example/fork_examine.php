<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Processor;
use SpencerMortensen\ParallelProcessor\Fork\Fork;

require 'bootstrap.php';

$results = array();

$processor = new Processor();

$t0 = microtime(true);

$processor->start(new Fork(new ExaminerJob('$x = 1;', $results[])));
$processor->start(new Fork(new ExaminerJob('require "xxx";', $results[])));

$processor->finish();

$t1 = microtime(true);

echo "results: ", json_encode($results), "\n";
echo "\nTotal time: ", $t1 - $t0, " seconds\n";
