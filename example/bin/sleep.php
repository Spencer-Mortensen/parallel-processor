<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Shell\ShellWorker;

require dirname(__DIR__) . '/bootstrap.php';

$duration = (integer)$GLOBALS['argv'][1];

$worker = new ShellWorker();
$job = new SleeperJob($duration);

$worker->run($job);
