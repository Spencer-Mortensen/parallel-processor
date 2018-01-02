<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Processor;
use SpencerMortensen\ParallelProcessor\Shell\Shell;

require 'bootstrap.php';

$results = array();

$processor = new Processor();

$t0 = microtime(true);

$processor->start(new Shell(new ExaminerJob('$x = 1;', $results[])));
$processor->start(new Shell(new ExaminerJob('require "xxx";', $results[])));

$processor->finish();

$t1 = microtime(true);

echo "results: ", json_encode($results), "\n";
echo "\nTotal time: ", $t1 - $t0, " seconds\n";
