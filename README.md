# parallel-processor

The "parallel-processor" is a PHP library that lets you run many pieces of code at once--and the act on the results as they arrive.

Here's an example:

```php
$processor = new ForkProcessor();

$t0 = microtime(true);

$processor->start(new SleepJob(2, $results[]));
$processor->start(new SleepJob(3, $results[]));
$processor->start(new SleepJob(1, $results[]));

$processor->finish();

$t1 = microtime(true);

echo implode("\n", $results), "\n";
echo "\nTotal time: ", $t1 - $t0, " seconds\n";
```

Here's the output:
```
Slept for 2 seconds.
Slept for 3 seconds.
Slept for 1 seconds.

Total time: 3.0033020973206 seconds
```

You can include this library through Composer:

> composer require [spencer-mortensen/parallel-processor](https://packagist.org/packages/spencer-mortensen/parallel-processor):~2.0
