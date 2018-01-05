<?php

call_user_func(function () {
	$projectDirectory = dirname(__DIR__);

	$classes = array(
		'Example' => "{$projectDirectory}/example/src",
		'SpencerMortensen\\Exceptions' => "{$projectDirectory}/vendor/spencer-mortensen/exceptions/src",
		'SpencerMortensen\\ParallelProcessor' => "{$projectDirectory}/src",
	);

	foreach ($classes as $namespacePrefix => $libraryPath) {
		$namespacePrefix .= '\\';
		$namespacePrefixLength = strlen($namespacePrefix);

		$autoloader = function ($class) use ($namespacePrefix, $namespacePrefixLength, $libraryPath) {
			if (strncmp($class, $namespacePrefix, $namespacePrefixLength) !== 0) {
				return;
			}

			$relativeClassName = substr($class, $namespacePrefixLength);
			$relativeFilePath = strtr($relativeClassName, '\\', '/') . '.php';
			$absoluteFilePath = "{$libraryPath}/{$relativeFilePath}";

			if (is_file($absoluteFilePath)) {
				include $absoluteFilePath;
			}
		};

		spl_autoload_register($autoloader);
	}
});
