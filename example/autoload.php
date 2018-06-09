<?php

namespace SpencerMortensen\Autoloader;

$project = dirname(__DIR__);

$classes = [
	'Example' => 'example/src',
	'SpencerMortensen\\Exceptions' => 'vendor/spencer-mortensen/exceptions/src',
	'SpencerMortensen\\ParallelProcessor' => 'src',
];

require "{$project}/vendor/spencer-mortensen/autoloader/src/Autoloader.php";

new Autoloader($project, $classes);
