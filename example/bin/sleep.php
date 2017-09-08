<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Shell\ShellSlave;

require dirname(__DIR__) . '/bootstrap.php';

$input = (integer)$argv[1];
$job = new SleepJob($input);

$slave = new ShellSlave($job);
$slave->run();
