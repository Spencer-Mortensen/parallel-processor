# parallel-processor

The "parallel-processor" is a PHP library that lets you run many pieces of code at once--and the act on the results as they arrive.

Here's an example:

```php
$processor = new Processor();

$t0 = microtime(true);

$processor->startJob('medium', new SleepJob(2));
$processor->startJob('large', new SleepJob(3));
$processor->startJob('small', new SleepJob(1));

while ($processor->getResult($id, $result)) {
    echo "{$id}: done\n";
}

$t1 = microtime(true);

echo "\n", "total time: ", $t1 - $t0, "\n";
```

Here's the output:
```
small: done
medium: done
large: done

total time: 3.0021669864655
```

You can see that this script finished in just three seconds--even though the jobs slept for a total of _six_ seconds. The parallel processor did everything in parallel.

You can include this library through Composer:

```bash
composer require [spencer-mortensen/parallel-processor](https://packagist.org/packages/spencer-mortensen/parallel-processor):~1.0
```