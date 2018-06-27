<?php

namespace Example;

use Exception;
use SpencerMortensen\Exceptions\Exceptions;
use SpencerMortensen\ParallelProcessor\Shell\ShellServerProcess;
use Throwable;

require dirname(__DIR__) . '/autoload.php';

try {
	Exceptions::on();

	$duration = (integer)$GLOBALS['argv'][1];

	$worker = new ShellServerProcess(new SleeperJob($duration));
	$worker->run();
} catch (Throwable $throwable) {
} catch (Exception $exception) {
} finally {
	Exceptions::off();
}
