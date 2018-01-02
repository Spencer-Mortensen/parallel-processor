<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Fork\ForkJob;
use SpencerMortensen\ParallelProcessor\Shell\ShellJob;
use SpencerMortensen\ParallelProcessor\Shell\ShellWorker;
use SpencerMortensen\ParallelProcessor\Shell\ShellWorkerJob;

class ExaminerJob implements ForkJob, ShellJob, ShellWorkerJob
{
	/** @var string */
	private $code;

	/** @var string */
	private $output;

	/** @var Examiner */
	private $examiner;

	/** @var ShellWorker */
	private $worker;

	public function __construct($code = null, &$output = null)
	{
		$this->code = $code;
		$this->output = &$output;
	}

	public function getCommand()
	{
		$scriptPath = dirname(__DIR__) . '/bin/examine.php';

		return 'php' .
			' ' . escapeshellarg($scriptPath) .
			' ' . escapeshellarg($this->code);
	}

	public function prepare(ShellWorker $worker)
	{
		$this->code = $GLOBALS['argv'][1];
		$this->examiner = new Examiner();
		$this->worker = $worker;
	}

	public function run()
	{
		$examiner = $this->examiner;
		$worker = $this->worker;

		$onShutdown = function () use ($examiner, $worker) {
			$result = $examiner->getState();
			$worker->reply($result);
		};

		$examiner->run($this->code, $onShutdown);

		call_user_func($onShutdown);
	}

	public function stop($message)
	{
		$this->output = $message;
	}
}
