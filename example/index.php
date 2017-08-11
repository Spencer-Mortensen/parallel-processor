<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Processor;

require 'bootstrap.php';

$processor = new Processor();

$t0 = microtime(true);

$processor->startJob('medium', new SleepJob(2));
$processor->startJob('large', new SleepJob(3));
$processor->startJob('small', new SleepJob(1));

while ($processor->getResult($id, $result)) {
	echo "{$id}: done\n";
}

$t1 = microtime(true);

echo "\ntotal time: ", $t1 - $t0, "\n";
