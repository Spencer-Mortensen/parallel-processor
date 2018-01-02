<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Shell\ShellWorker;

require dirname(__DIR__) . '/bootstrap.php';

call_user_func(function() {
	$worker = new ShellWorker();
	$job = new ExaminerJob();

	$job->prepare($worker);
	$worker->run($job);
});
