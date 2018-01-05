<?php

namespace Example;

use Exception;
use SpencerMortensen\Exceptions\Exceptions;
use SpencerMortensen\ParallelProcessor\Shell\ShellServerProcess;
use Throwable;

require dirname(__DIR__) . '/autoload.php';

Exceptions::on();

try {
	$duration = (integer)$GLOBALS['argv'][1];

	$worker = new ShellServerProcess(new SleeperJob($duration));
	$worker->run();
} catch (Throwable $throwable) {
} catch (Exception $exception) {
}

Exceptions::off();
